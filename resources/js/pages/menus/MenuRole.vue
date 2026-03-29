<template>
    <div class="flex flex-col gap-6">
        <section class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Menu Role</h2>
                    <p class="mt-1 text-sm text-slate-500">Configure menu permissions per role.</p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-slate-600" for="role-filter">Role</label>
                        <select
                            id="role-filter"
                            v-model.number="selectedRoleId"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-slate-400 focus:outline-none sm:w-56"
                            :disabled="rolesLoading || saving"
                            @change="fetchPermissions"
                        >
                            <option v-for="role in roles" :key="role.id" :value="role.id">
                                {{ role.name }}
                            </option>
                        </select>
                    </div>

                    <button
                        class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-full bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-60"
                        type="button"
                        :disabled="saving || !selectedRoleId"
                        @click="savePermissions"
                    >
                        <span
                            v-if="saving"
                            class="h-3 w-3 animate-spin rounded-full border-2 border-white/60 border-t-white"
                        ></span>
                        Update
                    </button>
                </div>
            </div>

            <div class="relative mt-6 overflow-x-auto overflow-y-visible">
                <Loading
                    :active="loading"
                    :is-full-page="overlayConfig.isFullPage"
                    :loader="overlayConfig.loader"
                    :color="overlayConfig.color"
                    :background-color="overlayConfig.backgroundColor"
                    :opacity="overlayConfig.opacity"
                />

                <table class="min-w-full text-left text-sm text-slate-600">
                    <thead class="text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-3 py-2">Menu</th>
                            <th v-for="method in methods" :key="method" class="px-3 py-2 text-center">
                                {{ method }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="menu in flatMenus" :key="menu.id">
                            <td class="px-3 py-3 font-medium text-slate-900">
                                <span class="text-slate-400" v-if="menu.depth">{{ '— '.repeat(menu.depth) }}</span>
                                {{ menu.name }}
                            </td>
                            <td v-for="method in methods" :key="`${menu.id}-${method}`" class="px-3 py-3 text-center">
                                <input
                                    class="h-4 w-4"
                                    type="checkbox"
                                    :disabled="loading || saving"
                                    v-model="matrix[menu.id][method]"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p v-if="flatMenus.length === 0 && !loading" class="mt-4 text-sm text-slate-500">
                    No menus found.
                </p>
                <p v-if="error" class="mt-4 text-sm text-rose-600">{{ error }}</p>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import { apiGet, apiPatch } from '../../fetch_api.js';
import { useConfigLoaderStore } from '../../states/config_loader';
import { toaster } from '../../utils/utils.js';

const roles = ref([]);
const rolesLoading = ref(false);
const selectedRoleId = ref(null);

const loading = ref(false);
const saving = ref(false);
const error = ref('');

const menuTree = ref([]);
const methods = ref(['get', 'post', 'patch', 'delete']);
const matrix = ref({});

const loaderConfig = useConfigLoaderStore();
const overlayConfig = computed(() => loaderConfig.overlay);

const flattenTree = (items, depth = 0, result = []) => {
    (items || []).forEach((item) => {
        result.push({
            id: item.id,
            name: item.name,
            depth,
        });
        if (item.children && item.children.length) {
            flattenTree(item.children, depth + 1, result);
        }
    });
    return result;
};

const flatMenus = computed(() => flattenTree(menuTree.value));

const buildEmptyMatrix = () => {
    const next = {};
    flatMenus.value.forEach((menu) => {
        next[menu.id] = methods.value.reduce((acc, method) => {
            acc[method] = false;
            return acc;
        }, {});
    });
    return next;
};

const fetchRoles = async () => {
    rolesLoading.value = true;
    try {
        const data = await apiGet('/api/role-list');
        roles.value = Array.isArray(data) ? data : data?.data || [];
        if (roles.value.length && !selectedRoleId.value) {
            selectedRoleId.value = roles.value[0].id;
        }
    } catch (err) {
        roles.value = [];
    } finally {
        rolesLoading.value = false;
    }
};

const fetchPermissions = async () => {
    if (!selectedRoleId.value) {
        return;
    }

    loading.value = true;
    error.value = '';
    try {
        const data = await apiGet(`/api/menu-role-permissions?role_id=${selectedRoleId.value}`);
        menuTree.value = data?.menus ?? [];
        methods.value = data?.methods ?? methods.value;

        const next = buildEmptyMatrix();
        const permissions = data?.permissions ?? {};
        Object.entries(permissions).forEach(([menuId, allowedMethods]) => {
            if (!next[menuId]) {
                return;
            }
            (allowedMethods || []).forEach((method) => {
                if (method in next[menuId]) {
                    next[menuId][method] = true;
                }
            });
        });
        matrix.value = next;
    } catch (err) {
        error.value = 'Failed to load permissions.';
        menuTree.value = [];
        matrix.value = {};
    } finally {
        loading.value = false;
    }
};

const savePermissions = async () => {
    if (!selectedRoleId.value) {
        return;
    }

    saving.value = true;
    error.value = '';

    try {
        const permissions = [];
        Object.entries(matrix.value || {}).forEach(([menuId, row]) => {
            Object.entries(row || {}).forEach(([method, enabled]) => {
                if (enabled) {
                    permissions.push({
                        menu_id: Number(menuId),
                        method,
                    });
                }
            });
        });

        await apiPatch('/api/menu-role-permissions', {
            role_id: selectedRoleId.value,
            permissions,
        });

        toaster('Permissions updated');
        await fetchPermissions();
    } catch (err) {
        error.value = 'Failed to update permissions.';
    } finally {
        saving.value = false;
    }
};

onMounted(async () => {
    await fetchRoles();
    await fetchPermissions();
});
</script>

