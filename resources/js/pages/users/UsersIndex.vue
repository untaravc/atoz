<template>
    <div>
        <div class="flex flex-col gap-6">
            <section class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Users</h2>
                    <div class="flex items-center gap-2">
                        <button
                            class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                            type="button"
                            :disabled="refreshing"
                            @click="refreshUsers"
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
                            Create user
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
                                <th class="px-3 py-2">Email</th>
                                <th class="px-3 py-2">Role</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(user, index) in users" :key="user.id">
                                <td class="px-3 py-3 font-medium text-slate-900">{{ user.name }}</td>
                                <td class="px-3 py-3">{{ user.email }}</td>
                                <td class="px-3 py-3">{{ user.role_id ?? '-' }}</td>
                                <td class="px-3 py-3 text-right">
                                    <div class="relative inline-flex justify-end">
                                        <button
                                            class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50"
                                            type="button"
                                            :aria-expanded="openMenuId === user.id"
                                            aria-label="Open actions"
                                            @click="toggleMenu(user.id)"
                                        >
                                            <VIcon name="fa-ellipsis-v" class="h-4 w-4" />
                                        </button>
                                        <div
                                            v-if="openMenuId === user.id"
                                            class="absolute right-0 z-10 w-32 rounded-lg border border-slate-200 bg-white py-1 text-left text-xs shadow-lg"
                                            :class="isBottomMenu(index) ? 'bottom-10' : 'top-10'"
                                        >
                                            <button
                                                class="block w-full px-3 py-2 text-left text-slate-600 hover:bg-slate-50"
                                                type="button"
                                                @click="startEdit(user)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                class="block w-full px-3 py-2 text-left text-rose-600 hover:bg-rose-50"
                                                type="button"
                                                @click="openDelete(user)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-if="users.length === 0 && !loading" class="mt-4 text-sm text-slate-500">No users found.</p>
                    <p v-if="loading" class="mt-4 text-sm text-slate-500">Loading users...</p>
                </div>
                <PaginationNav
                    :current-page="currentPage"
                    :last-page="lastPage"
                    :total="totalItems || users.length"
                    :from="users.length ? (currentPage - 1) * perPage + 1 : 0"
                    :to="Math.min(currentPage * perPage, totalItems || users.length)"
                    :loading="loading"
                    @page-change="goToPage"
                />
            </section>
        </div>

        <Modal
            :open="formOpen"
            :title="editing ? 'Edit user' : 'Create user'"
            eyebrow="Users"
            @close="closeForm"
        >
            <form class="space-y-4" @submit.prevent="handleSubmit">
                <div>
                    <label class="text-sm font-medium text-slate-600" for="user-name">Name</label>
                    <input
                        id="user-name"
                        v-model="form.name"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. Jane Doe"
                        required
                        type="text"
                    />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600" for="user-email">Email</label>
                    <input
                        id="user-email"
                        v-model="form.email"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="jane@example.com"
                        required
                        type="email"
                    />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600" for="user-password">Password</label>
                    <input
                        id="user-password"
                        v-model="form.password"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="Leave blank to keep current"
                        :required="!editing"
                        type="password"
                    />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600" for="user-role">Role ID</label>
                    <input
                        id="user-role"
                        v-model.number="form.role_id"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="Optional"
                        type="number"
                    />
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
                        {{ editing ? 'Update user' : 'Create user' }}
                    </button>
                </div>
            </form>
        </Modal>

        <Modal :open="deleteOpen" title="Delete user" eyebrow="Users" @close="closeDelete">
            <p class="text-sm text-slate-600">
                Are you sure you want to delete
                <span class="font-semibold text-slate-900">{{ selectedUser?.name }}</span>?
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
import { reactive, ref, onMounted, computed } from 'vue';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import Modal from '../components/Modal.vue';
import PaginationNav from '../components/PaginationNav.vue';
import { apiDelete, apiGet, apiPatch, apiPost } from '../../fetch_api.js';
import { useConfigLoaderStore } from '../../states/config_loader';
import { toaster } from '../../utils/utils.js';

const users = ref([]);
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
const selectedUser = ref(null);
const openMenuId = ref(null);
const form = reactive({
    id: null,
    name: '',
    email: '',
    password: '',
    role_id: null,
});

const loaderConfig = useConfigLoaderStore();
const overlayConfig = computed(() => loaderConfig.overlay);

const fetchUsers = async (page = currentPage.value) => {
    loading.value = true;
    error.value = '';
    try {
        const data = await apiGet(`/api/users?page=${page}`);
        users.value = Array.isArray(data) ? data : data.data || [];
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
            totalItems.value = users.value.length;
        }
    } catch (err) {
        error.value = 'Failed to load users.';
    } finally {
        loading.value = false;
    }
};

const refreshUsers = async () => {
    if (refreshing.value) {
        return;
    }
    refreshing.value = true;
    await fetchUsers(currentPage.value);
    refreshing.value = false;
};

const goToPage = async (page) => {
    if (loading.value || page < 1 || page > lastPage.value || page === currentPage.value) {
        return;
    }
    await fetchUsers(page);
};

const resetForm = () => {
    form.id = null;
    form.name = '';
    form.email = '';
    form.password = '';
    form.role_id = null;
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

const toggleMenu = (userId) => {
    openMenuId.value = openMenuId.value === userId ? null : userId;
};

const closeMenu = () => {
    openMenuId.value = null;
};

const isBottomMenu = (index) => {
    return users.value.length - index <= 2;
};

const handleSubmit = async () => {
    error.value = '';
    saving.value = true;
    const payload = {
        name: form.name.trim(),
        email: form.email.trim(),
    };

    if (form.role_id) {
        payload.role_id = form.role_id;
    }

    if (form.password) {
        payload.password = form.password;
    }

    try {
        if (editing.value) {
            await apiPatch(`/api/users/${form.id}`, payload);
            toaster('User updated');
        } else {
            if (!payload.password) {
                error.value = 'Password is required.';
                saving.value = false;
                return;
            }
            await apiPost('/api/users', payload);
            toaster('User created');
        }

        formOpen.value = false;
        resetForm();
        await fetchUsers();
    } catch (err) {
        error.value = 'Failed to save user.';
    } finally {
        saving.value = false;
    }
};

const startEdit = (user) => {
    closeMenu();
    form.id = user.id;
    form.name = user.name || '';
    form.email = user.email || '';
    form.password = '';
    form.role_id = user.role_id ?? null;
    editing.value = true;
    formOpen.value = true;
};

const openDelete = (user) => {
    closeMenu();
    selectedUser.value = user;
    deleteOpen.value = true;
};

const closeDelete = () => {
    deleteOpen.value = false;
    selectedUser.value = null;
};

const confirmDelete = async () => {
    if (!selectedUser.value) {
        return;
    }

    error.value = '';
    deleting.value = true;

    try {
        await apiDelete(`/api/users/${selectedUser.value.id}`);
        toaster('User deleted');
        await fetchUsers();
        if (editing.value && form.id === selectedUser.value.id) {
            resetForm();
        }
        closeDelete();
        closeMenu();
    } catch (err) {
        error.value = 'Failed to delete user.';
    } finally {
        deleting.value = false;
    }
};

onMounted(fetchUsers);
</script>
