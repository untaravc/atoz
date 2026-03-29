<template>
    <div>
        <div class="flex flex-col gap-6">
            <section class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Categories</h2>
                    <div class="flex items-center gap-2">
                        <button
                            class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                            type="button"
                            :disabled="refreshing"
                            @click="refreshCategories"
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
                            Create category
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
                                <th class="px-3 py-2">Code</th>
                                <th class="px-3 py-2">Name</th>
                                <th class="px-3 py-2">Section</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(category, index) in categories" :key="category.id">
                                <td class="px-3 py-3 font-medium text-slate-900">{{ category.code }}</td>
                                <td class="px-3 py-3">{{ category.name }}</td>
                                <td class="px-3 py-3">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        {{ category.section }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="category.status ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-500'"
                                    >
                                        {{ category.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-right">
                                    <div class="relative inline-flex justify-end" data-category-menu>
                                        <button
                                            class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50"
                                            type="button"
                                            :aria-expanded="openMenuId === category.id"
                                            aria-label="Open actions"
                                            @click="toggleMenu(category.id)"
                                            data-category-menu
                                        >
                                            <VIcon name="fa-ellipsis-v" class="h-4 w-4" />
                                        </button>
                                        <div
                                            v-if="openMenuId === category.id"
                                            class="absolute right-0 z-10 w-32 rounded-lg border border-slate-200 bg-white py-1 text-left text-xs shadow-lg"
                                            :class="isBottomMenu(index) ? 'bottom-10' : 'top-10'"
                                            data-category-menu
                                        >
                                            <button
                                                class="block w-full px-3 py-2 text-left text-slate-600 hover:bg-slate-50"
                                                type="button"
                                                @click="startEdit(category)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                class="block w-full px-3 py-2 text-left text-rose-600 hover:bg-rose-50"
                                                type="button"
                                                @click="openDelete(category)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-if="categories.length === 0 && !loading" class="mt-4 text-sm text-slate-500">
                        No categories found.
                    </p>
                    <p v-if="loading" class="mt-4 text-sm text-slate-500">Loading categories...</p>
                </div>

                <PaginationNav
                    :current-page="currentPage"
                    :last-page="lastPage"
                    :total="totalItems || categories.length"
                    :from="categories.length ? (currentPage - 1) * perPage + 1 : 0"
                    :to="Math.min(currentPage * perPage, totalItems || categories.length)"
                    :loading="loading"
                    @page-change="goToPage"
                />
            </section>
        </div>

        <Modal
            :open="formOpen"
            :title="editing ? 'Edit category' : 'Create category'"
            eyebrow="Categories"
            @close="closeForm"
        >
            <form class="space-y-4" @submit.prevent="handleSubmit">
                <div>
                    <label class="text-sm font-medium text-slate-600" for="category-code">Code</label>
                    <input
                        id="category-code"
                        v-model="form.code"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. TXN"
                        required
                        type="text"
                    />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600" for="category-name">Name</label>
                    <input
                        id="category-name"
                        v-model="form.name"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. Transaction"
                        required
                        type="text"
                    />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600" for="category-section">Section</label>
                    <select
                        id="category-section"
                        v-model="form.section"
                        class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        required
                    >
                        <option value="transaction">transaction</option>
                        <option value="transaction_status">transaction_status</option>
                        <option value="price">price</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input id="category-status" v-model="form.status" class="h-4 w-4" type="checkbox" />
                    <label class="text-sm text-slate-600" for="category-status">Active</label>
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
                        {{ editing ? 'Update category' : 'Create category' }}
                    </button>
                </div>
            </form>
        </Modal>

        <Modal :open="deleteOpen" title="Delete category" eyebrow="Categories" @close="closeDelete">
            <p class="text-sm text-slate-600">
                Are you sure you want to delete
                <span class="font-semibold text-slate-900">{{ selectedCategory?.name }}</span>?
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
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import Modal from '../components/Modal.vue';
import PaginationNav from '../components/PaginationNav.vue';
import { apiDelete, apiGet, apiPatch, apiPost } from '../../fetch_api.js';
import { useConfigLoaderStore } from '../../states/config_loader';
import { toaster } from '../../utils/utils.js';

const categories = ref([]);
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
const selectedCategory = ref(null);
const openMenuId = ref(null);
const form = reactive({
    id: null,
    code: '',
    name: '',
    section: 'transaction',
    status: true,
});

const loaderConfig = useConfigLoaderStore();
const overlayConfig = computed(() => loaderConfig.overlay);

const fetchCategories = async (page = currentPage.value) => {
    loading.value = true;
    error.value = '';
    try {
        const data = await apiGet(`/api/categories?page=${page}`);
        categories.value = Array.isArray(data) ? data : data.data || [];
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
            totalItems.value = categories.value.length;
        }
    } catch (err) {
        error.value = 'Failed to load categories.';
    } finally {
        loading.value = false;
    }
};

const refreshCategories = async () => {
    if (refreshing.value) {
        return;
    }
    refreshing.value = true;
    await fetchCategories(currentPage.value);
    refreshing.value = false;
};

const goToPage = async (page) => {
    if (loading.value || page < 1 || page > lastPage.value || page === currentPage.value) {
        return;
    }
    await fetchCategories(page);
};

const toggleMenu = (categoryId) => {
    openMenuId.value = openMenuId.value === categoryId ? null : categoryId;
};

const closeMenu = () => {
    openMenuId.value = null;
};

const isBottomMenu = (index) => {
    return categories.value.length - index <= 2;
};

const handleDocumentClick = (event) => {
    if (!openMenuId.value) {
        return;
    }
    const target = event.target;
    if (target && target.closest && target.closest('[data-category-menu]')) {
        return;
    }
    closeMenu();
};

const resetForm = () => {
    form.id = null;
    form.code = '';
    form.name = '';
    form.section = 'transaction';
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
    saving.value = true;

    const payload = {
        code: form.code.trim(),
        name: form.name.trim(),
        section: form.section,
        status: Boolean(form.status),
    };

    try {
        if (editing.value) {
            await apiPatch(`/api/categories/${form.id}`, payload);
            toaster('Category updated');
        } else {
            await apiPost('/api/categories', payload);
            toaster('Category created');
        }

        formOpen.value = false;
        resetForm();
        await fetchCategories();
    } catch (err) {
        error.value = 'Failed to save category.';
    } finally {
        saving.value = false;
    }
};

const startEdit = (category) => {
    closeMenu();
    form.id = category.id;
    form.code = category.code || '';
    form.name = category.name || '';
    form.section = category.section || 'transaction';
    form.status = Boolean(category.status);
    editing.value = true;
    formOpen.value = true;
};

const openDelete = (category) => {
    closeMenu();
    selectedCategory.value = category;
    deleteOpen.value = true;
};

const closeDelete = () => {
    deleteOpen.value = false;
    selectedCategory.value = null;
};

const confirmDelete = async () => {
    if (!selectedCategory.value) {
        return;
    }

    error.value = '';
    deleting.value = true;

    try {
        await apiDelete(`/api/categories/${selectedCategory.value.id}`);
        toaster('Category deleted');
        await fetchCategories();
        if (editing.value && form.id === selectedCategory.value.id) {
            resetForm();
        }
        closeDelete();
        closeMenu();
    } catch (err) {
        error.value = 'Failed to delete category.';
    } finally {
        deleting.value = false;
    }
};

onMounted(() => {
    fetchCategories();
    document.addEventListener('click', handleDocumentClick);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
});
</script>

