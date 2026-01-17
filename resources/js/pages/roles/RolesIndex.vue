<template>
    <div>
        <div class="flex flex-col gap-6">
            <section class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Roles</h2>
                    <div class="flex items-center gap-2">
                        <button
                            class="rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                            type="button"
                            @click="fetchRoles"
                        >
                            Refresh
                        </button>
                        <button
                            class="rounded-full bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/90"
                            type="button"
                            @click="openCreate"
                        >
                            Create role
                        </button>
                    </div>
                </div>
                <div class="relative mt-6 overflow-x-auto">
                    <Loading
                        :active="loading"
                        :is-full-page="false"
                        loader="dots"
                        color="#0f172a"
                        background-color="#ffffff"
                        opacity="0.7"
                    />
                    <table class="min-w-full text-left text-sm text-slate-600">
                        <thead class="text-xs uppercase tracking-wide text-slate-400">
                            <tr>
                                <th class="px-3 py-2">Name</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="role in roles" :key="role.id">
                                <td class="px-3 py-3 font-medium text-slate-900">{{ role.name }}</td>
                                <td class="px-3 py-3">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="role.status ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-500'"
                                    >
                                        {{ role.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-right">
                                    <button
                                        class="rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                                        type="button"
                                        @click="startEdit(role)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        class="ml-2 rounded-lg border border-rose-200 px-3 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                                        type="button"
                                        @click="openDelete(role)"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-if="roles.length === 0 && !loading" class="mt-4 text-sm text-slate-500">No roles found.</p>
                    <p v-if="loading" class="mt-4 text-sm text-slate-500">Loading roles...</p>
                </div>
            </section>
        </div>

        <Modal
            :open="formOpen"
            :title="editing ? 'Edit role' : 'Create role'"
            eyebrow="Roles"
            @close="closeForm"
        >
            <form class="space-y-4" @submit.prevent="handleSubmit">
                <div>
                    <label class="text-sm font-medium text-slate-600" for="role-name">Role name</label>
                    <input
                        id="role-name"
                        v-model="form.name"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. Manager"
                        required
                        type="text"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <input id="role-status" v-model="form.status" class="h-4 w-4" type="checkbox" />
                    <label class="text-sm text-slate-600" for="role-status">Active</label>
                </div>
                <p v-if="error" class="text-xs text-rose-500">{{ error }}</p>
                <div class="flex items-center justify-end gap-2">
                    <button
                        class="rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                        type="button"
                        @click="closeForm"
                    >
                        Cancel
                    </button>
                    <button
                        class="rounded-full bg-primary px-4 py-2 text-xs font-semibold text-white hover:bg-primary/90"
                        type="submit"
                    >
                        {{ editing ? 'Update role' : 'Create role' }}
                    </button>
                </div>
            </form>
        </Modal>

        <Modal :open="deleteOpen" title="Delete role" eyebrow="Roles" @close="closeDelete">
            <p class="text-sm text-slate-600">
                Are you sure you want to delete
                <span class="font-semibold text-slate-900">{{ selectedRole?.name }}</span>?
            </p>
            <template #footer>
                <button
                    class="rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                    type="button"
                    @click="closeDelete"
                >
                    Cancel
                </button>
                <button
                    class="rounded-full bg-rose-600 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-500"
                    type="button"
                    @click="confirmDelete"
                >
                    Delete
                </button>
            </template>
        </Modal>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import Modal from '../components/Modal.vue';

const roles = ref([]);
const loading = ref(false);
const error = ref('');
const editing = ref(false);
const formOpen = ref(false);
const deleteOpen = ref(false);
const selectedRole = ref(null);
const form = reactive({
    id: null,
    name: '',
    status: true,
});

const fetchRoles = async () => {
    loading.value = true;
    error.value = '';
    try {
        const response = await fetch('/api/roles');
        const data = await response.json();
        roles.value = Array.isArray(data) ? data : data.data || [];
    } catch (err) {
        error.value = 'Failed to load roles.';
    } finally {
        loading.value = false;
    }
};

const resetForm = () => {
    form.id = null;
    form.name = '';
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

const handleSubmit = async () => {
    error.value = '';
    const payload = {
        name: form.name.trim(),
        status: form.status,
    };

    try {
        const response = await fetch(`/api/roles${editing.value ? `/${form.id}` : ''}`, {
            method: editing.value ? 'PATCH' : 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            throw new Error('Request failed');
        }

        await fetchRoles();
        resetForm();
        formOpen.value = false;
    } catch (err) {
        error.value = 'Failed to save role.';
    }
};

const startEdit = (role) => {
    form.id = role.id;
    form.name = role.name || '';
    form.status = Boolean(role.status);
    editing.value = true;
    formOpen.value = true;
};

const openDelete = (role) => {
    selectedRole.value = role;
    deleteOpen.value = true;
};

const closeDelete = () => {
    deleteOpen.value = false;
    selectedRole.value = null;
};

const confirmDelete = async () => {
    if (!selectedRole.value) {
        return;
    }

    error.value = '';

    try {
        const response = await fetch(`/api/roles/${selectedRole.value.id}`, { method: 'DELETE' });
        if (!response.ok) {
            throw new Error('Delete failed');
        }
        await fetchRoles();
        if (editing.value && form.id === selectedRole.value.id) {
            resetForm();
        }
        closeDelete();
    } catch (err) {
        error.value = 'Failed to delete role.';
    }
};

onMounted(fetchRoles);
</script>
