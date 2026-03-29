<template>
    <div>
        <div class="flex flex-col gap-6">
            <section class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Menus</h2>
                    <div class="flex items-center gap-2">
                        <button
                            class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                            type="button"
                            :disabled="refreshing"
                            @click="refreshMenus"
                        >
                            <span
                                v-if="refreshing"
                                class="h-3 w-3 animate-spin rounded-full border-2 border-slate-300 border-t-slate-600"
                            ></span>
                            Refresh
                        </button>
                        <button
                            class="cursor-pointer rounded-full bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/90"
                            type="button"
                            @click="openCreate"
                        >
                            Create menu
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
                                <th class="px-3 py-2">Name</th>
                                <th class="px-3 py-2">Route</th>
                                <th class="px-3 py-2">Parent</th>
                                <th class="px-3 py-2">Sort</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(menu, index) in menus" :key="menu.id">
                                <td class="px-3 py-3 font-medium text-slate-900">{{ menu.name }}</td>
                                <td class="px-3 py-3">
                                    <span class="text-slate-700">{{ menu.route || '-' }}</span>
                                </td>
                                <td class="px-3 py-3">
                                    <span class="text-slate-700">{{ parentName(menu.parent_id) }}</span>
                                </td>
                                <td class="px-3 py-3">{{ menu.sort_order ?? '-' }}</td>
                                <td class="px-3 py-3">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="menu.status ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-500'"
                                    >
                                        {{ menu.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-right">
                                    <div class="relative inline-flex justify-end" data-menu-actions>
                                        <button
                                            class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50"
                                            type="button"
                                            :aria-expanded="openMenuId === menu.id"
                                            aria-label="Open actions"
                                            @click="toggleMenu(menu.id)"
                                            data-menu-actions
                                        >
                                            <VIcon name="fa-ellipsis-v" class="h-4 w-4" />
                                        </button>
                                        <div
                                            v-if="openMenuId === menu.id"
                                            class="absolute right-0 z-10 w-32 rounded-lg border border-slate-200 bg-white py-1 text-left text-xs shadow-lg"
                                            :class="isBottomMenu(index) ? 'bottom-10' : 'top-10'"
                                            data-menu-actions
                                        >
                                            <button
                                                class="block w-full px-3 py-2 text-left text-slate-600 hover:bg-slate-50"
                                                type="button"
                                                @click="startEdit(menu)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                class="block w-full px-3 py-2 text-left text-rose-600 hover:bg-rose-50"
                                                type="button"
                                                @click="openDelete(menu)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-if="menus.length === 0 && !loading" class="mt-4 text-sm text-slate-500">No menus found.</p>
                    <p v-if="loading" class="mt-4 text-sm text-slate-500">Loading menus...</p>
                </div>

                <PaginationNav
                    :current-page="currentPage"
                    :last-page="lastPage"
                    :total="totalItems || menus.length"
                    :from="menus.length ? (currentPage - 1) * perPage + 1 : 0"
                    :to="Math.min(currentPage * perPage, totalItems || menus.length)"
                    :loading="loading"
                    @page-change="goToPage"
                />
            </section>
        </div>

        <Modal
            :open="formOpen"
            :title="editing ? 'Edit menu' : 'Create menu'"
            eyebrow="Menus"
            @close="closeForm"
        >
            <form class="space-y-4" @submit.prevent="handleSubmit">
                <div>
                    <label class="text-sm font-medium text-slate-600" for="menu-name">Name</label>
                    <input
                        id="menu-name"
                        v-model="form.name"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. Users"
                        required
                        type="text"
                    />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600" for="menu-route">Route</label>
                    <input
                        id="menu-route"
                        v-model="form.route"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. /bo/users"
                        type="text"
                    />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600" for="menu-icon">Icon</label>
                    <input
                        id="menu-icon"
                        v-model="form.icon"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. fa-users"
                        type="text"
                    />
                    <p class="mt-1 text-xs text-slate-400">Use a VIcon name (e.g. FontAwesome class).</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600" for="menu-parent">Parent</label>
                    <select
                        id="menu-parent"
                        v-model="form.parent_id"
                        class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                    >
                        <option :value="null">No parent</option>
                        <option
                            v-for="option in availableParents"
                            :key="option.id"
                            :value="option.id"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600" for="menu-sort">Sort order</label>
                    <input
                        id="menu-sort"
                        v-model.number="form.sort_order"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. 10"
                        type="number"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <input id="menu-status" v-model="form.status" class="h-4 w-4" type="checkbox" />
                    <label class="text-sm text-slate-600" for="menu-status">Active</label>
                </div>

                <p v-if="error" class="text-xs text-rose-500">{{ error }}</p>
                <div class="flex items-center justify-end gap-2">
                    <button
                        class="cursor-pointer rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                        type="button"
                        :disabled="saving"
                        @click="closeForm"
                    >
                        Cancel
                    </button>
                    <button
                        class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/90"
                        type="submit"
                        :disabled="saving"
                    >
                        <span
                            v-if="saving"
                            class="h-3 w-3 animate-spin rounded-full border-2 border-white/60 border-t-white"
                        ></span>
                        {{ editing ? 'Update menu' : 'Create menu' }}
                    </button>
                </div>
            </form>
        </Modal>

        <Modal :open="deleteOpen" title="Delete menu" eyebrow="Menus" @close="closeDelete">
            <p class="text-sm text-slate-600">
                Are you sure you want to delete
                <span class="font-semibold text-slate-900">{{ selectedMenu?.name }}</span>?
            </p>
            <template #footer>
                <button
                    class="cursor-pointer rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                    type="button"
                    :disabled="deleting"
                    @click="closeDelete"
                >
                    Cancel
                </button>
                <button
                    class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-500"
                    type="button"
                    :disabled="deleting"
                    @click="confirmDelete"
                >
                    <span
                        v-if="deleting"
                        class="h-3 w-3 animate-spin rounded-full border-2 border-white/60 border-t-white"
                    ></span>
                    Delete
                </button>
            </template>
        </Modal>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted, onBeforeUnmount, computed } from 'vue';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import Modal from '../components/Modal.vue';
import PaginationNav from '../components/PaginationNav.vue';
import { apiDelete, apiGet, apiPatch, apiPost } from '../../fetch_api.js';
import { useConfigLoaderStore } from '../../states/config_loader';
import { toaster } from '../../utils/utils.js';

const menus = ref([]);
const menuTree = ref([]);
const loading = ref(false);
const refreshing = ref(false);
const saving = ref(false);
const deleting = ref(false);
const currentPage = ref(1);
const lastPage = ref(1);
const totalItems = ref(0);
const perPage = ref(15);
const error = ref('');
const editing = ref(false);
const formOpen = ref(false);
const deleteOpen = ref(false);
const selectedMenu = ref(null);
const openMenuId = ref(null);
const form = reactive({
    id: null,
    name: '',
    route: '',
    icon: '',
    parent_id: null,
    sort_order: null,
    status: true,
});

const loaderConfig = useConfigLoaderStore();
const overlayConfig = computed(() => loaderConfig.overlay);

const fetchMenus = async (page = currentPage.value) => {
    loading.value = true;
    error.value = '';
    try {
        const data = await apiGet(`/api/menus?page=${page}`);
        menus.value = Array.isArray(data) ? data : data.data || [];
        if (data && typeof data === 'object' && 'current_page' in data) {
            currentPage.value = data.current_page ?? page;
            lastPage.value = data.last_page ?? 1;
            totalItems.value = data.total ?? 0;
            perPage.value = Number(data.per_page ?? perPage.value);
        } else if (data?.meta) {
            currentPage.value = data.meta.current_page ?? page;
            lastPage.value = data.meta.last_page ?? 1;
            totalItems.value = data.meta.total ?? 0;
            perPage.value = data.meta.per_page ?? perPage.value;
        } else {
            currentPage.value = 1;
            lastPage.value = 1;
            totalItems.value = menus.value.length;
        }
    } catch (err) {
        error.value = 'Failed to load menus.';
    } finally {
        loading.value = false;
    }
};

const fetchMenuTree = async () => {
    try {
        const data = await apiGet('/api/menu');
        menuTree.value = data?.result ?? data ?? [];
    } catch (err) {
        menuTree.value = [];
    }
};

const refreshMenus = async () => {
    if (refreshing.value) {
        return;
    }
    refreshing.value = true;
    await Promise.all([fetchMenus(currentPage.value), fetchMenuTree()]);
    refreshing.value = false;
};

const goToPage = async (page) => {
    if (loading.value || page < 1 || page > lastPage.value || page === currentPage.value) {
        return;
    }
    await fetchMenus(page);
};

const toggleMenu = (menuId) => {
    openMenuId.value = openMenuId.value === menuId ? null : menuId;
};

const closeMenu = () => {
    openMenuId.value = null;
};

const isBottomMenu = (index) => {
    return menus.value.length - index <= 2;
};

const handleDocumentClick = (event) => {
    if (!openMenuId.value) {
        return;
    }
    const target = event.target;
    if (target && target.closest && target.closest('[data-menu-actions]')) {
        return;
    }
    closeMenu();
};

const flattenTree = (items, depth = 0, result = []) => {
    (items || []).forEach((item) => {
        result.push({
            id: item.id,
            name: item.name,
            label: `${'— '.repeat(depth)}${item.name}`,
        });
        if (item.children && item.children.length) {
            flattenTree(item.children, depth + 1, result);
        }
    });
    return result;
};

const parentMap = computed(() => {
    const map = new Map();
    flattenTree(menuTree.value).forEach((node) => map.set(node.id, node.name));
    menus.value.forEach((node) => {
        if (node && node.id != null && node.name != null) {
            map.set(node.id, node.name);
        }
    });
    return map;
});

const parentName = (parentId) => {
    if (!parentId) {
        return '-';
    }
    return parentMap.value.get(parentId) || `#${parentId}`;
};

const availableParents = computed(() => {
    const all = flattenTree(menuTree.value);
    if (!editing.value || !form.id) {
        return all;
    }
    return all.filter((option) => option.id !== form.id);
});

const resetForm = () => {
    form.id = null;
    form.name = '';
    form.route = '';
    form.icon = '';
    form.parent_id = null;
    form.sort_order = null;
    form.status = true;
    editing.value = false;
};

const openCreate = () => {
    resetForm();
    formOpen.value = true;
};

const closeForm = () => {
    formOpen.value = false;
    if (!editing.value) {
        resetForm();
    }
};

const normalizeOptionalText = (value) => {
    const text = (value ?? '').toString().trim();
    return text.length ? text : null;
};

const handleSubmit = async () => {
    error.value = '';
    saving.value = true;

    const payload = {
        name: form.name.trim(),
        route: normalizeOptionalText(form.route),
        icon: normalizeOptionalText(form.icon),
        parent_id: form.parent_id ? Number(form.parent_id) : null,
        sort_order: form.sort_order === null || form.sort_order === '' ? null : Number(form.sort_order),
        status: Boolean(form.status),
    };

    if (editing.value && payload.parent_id && payload.parent_id === form.id) {
        error.value = 'Parent cannot be the menu itself.';
        saving.value = false;
        return;
    }

    try {
        if (editing.value) {
            await apiPatch(`/api/menus/${form.id}`, payload);
            toaster('Menu updated');
        } else {
            await apiPost('/api/menus', payload);
            toaster('Menu created');
        }

        formOpen.value = false;
        resetForm();
        await Promise.all([fetchMenus(), fetchMenuTree()]);
    } catch (err) {
        error.value = 'Failed to save menu.';
    } finally {
        saving.value = false;
    }
};

const startEdit = (menu) => {
    closeMenu();
    form.id = menu.id;
    form.name = menu.name || '';
    form.route = menu.route || '';
    form.icon = menu.icon || '';
    form.parent_id = menu.parent_id ?? null;
    form.sort_order = menu.sort_order ?? null;
    form.status = Boolean(menu.status);
    editing.value = true;
    formOpen.value = true;
};

const openDelete = (menu) => {
    closeMenu();
    selectedMenu.value = menu;
    deleteOpen.value = true;
};

const closeDelete = () => {
    deleteOpen.value = false;
    selectedMenu.value = null;
};

const confirmDelete = async () => {
    if (!selectedMenu.value) {
        return;
    }

    error.value = '';
    deleting.value = true;

    try {
        await apiDelete(`/api/menus/${selectedMenu.value.id}`);
        toaster('Menu deleted');
        await Promise.all([fetchMenus(), fetchMenuTree()]);
        if (editing.value && form.id === selectedMenu.value.id) {
            resetForm();
        }
        closeDelete();
        closeMenu();
    } catch (err) {
        error.value = 'Failed to delete menu.';
    } finally {
        deleting.value = false;
    }
};

onMounted(async () => {
    await Promise.all([fetchMenus(), fetchMenuTree()]);
    document.addEventListener('click', handleDocumentClick);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
});
</script>

