<template>
    <div>
        <div class="flex flex-col gap-6">
            <section class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Transactions</h2>
                    <div class="flex items-center gap-2">
                        <button
                            class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                            type="button"
                            :disabled="refreshing"
                            @click="refreshTransactions"
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
                            Create transaction
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
                                <th class="px-3 py-2">Number</th>
                                <th class="px-3 py-2">Name</th>
                                <th class="px-3 py-2">Origin</th>
                                <th class="px-3 py-2">Destination</th>
                                <th class="px-3 py-2">Category</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(txn, index) in transactions" :key="txn.id">
                                <td class="px-3 py-3 font-medium text-slate-900">{{ txn.number }}</td>
                                <td class="px-3 py-3">{{ txn.name }}</td>
                                <td class="px-3 py-3">
                                    {{ txn.origin_office_code ? `${txn.origin_office_code} - ${txn.origin_office_name || ''}` : '-' }}
                                </td>
                                <td class="px-3 py-3">
                                    {{
                                        txn.destination_office_code
                                            ? `${txn.destination_office_code} - ${txn.destination_office_name || ''}`
                                            : '-'
                                    }}
                                </td>
                                <td class="px-3 py-3">{{ txn.category_name || '-' }}</td>
                                <td class="px-3 py-3">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                        {{ txn.status ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-right">
                                    <div class="relative inline-flex justify-end" data-txn-menu>
                                        <button
                                            class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50"
                                            type="button"
                                            :aria-expanded="openMenuId === txn.id"
                                            aria-label="Open actions"
                                            @click="toggleMenu(txn.id)"
                                            data-txn-menu
                                        >
                                            <VIcon name="fa-ellipsis-v" class="h-4 w-4" />
                                        </button>
                                        <div
                                            v-if="openMenuId === txn.id"
                                            class="absolute right-0 z-10 w-32 rounded-lg border border-slate-200 bg-white py-1 text-left text-xs shadow-lg"
                                            :class="isBottomMenu(index) ? 'bottom-10' : 'top-10'"
                                            data-txn-menu
                                        >
                                            <button
                                                class="block w-full px-3 py-2 text-left text-slate-600 hover:bg-slate-50"
                                                type="button"
                                                @click="startEdit(txn)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                class="block w-full px-3 py-2 text-left text-rose-600 hover:bg-rose-50"
                                                type="button"
                                                @click="openDelete(txn)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p v-if="transactions.length === 0 && !loading" class="mt-4 text-sm text-slate-500">
                        No transactions found.
                    </p>
                    <p v-if="loading" class="mt-4 text-sm text-slate-500">Loading transactions...</p>
                </div>

                <PaginationNav
                    :current-page="currentPage"
                    :last-page="lastPage"
                    :total="totalItems || transactions.length"
                    :from="transactions.length ? (currentPage - 1) * perPage + 1 : 0"
                    :to="Math.min(currentPage * perPage, totalItems || transactions.length)"
                    :loading="loading"
                    @page-change="goToPage"
                />
            </section>
        </div>

        <Modal
            :open="formOpen"
            :title="editing ? 'Edit transaction' : 'Create transaction'"
            eyebrow="Transactions"
            @close="closeForm"
        >
            <form class="space-y-4" @submit.prevent="handleSubmit">
                <div>
                    <label class="text-sm font-medium text-slate-600" for="txn-number">Number</label>
                    <input
                        id="txn-number"
                        :value="editing ? form.number : 'Auto-generated'"
                        class="mt-2 w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500 focus:outline-none"
                        readonly
                        type="text"
                    />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="txn-origin">Origin office</label>
                        <select
                            id="txn-origin"
                            v-model.number="form.origin_office_id"
                            class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            required
                        >
                            <option :value="null" disabled>Select office</option>
                            <option v-for="office in offices" :key="office.id" :value="office.id">
                                {{ office.code }} - {{ office.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="txn-destination">Destination office</label>
                        <select
                            id="txn-destination"
                            v-model.number="form.destination_office_id"
                            class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            required
                        >
                            <option :value="null" disabled>Select office</option>
                            <option v-for="office in offices" :key="office.id" :value="office.id">
                                {{ office.code }} - {{ office.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="txn-category">Category</label>
                        <select
                            id="txn-category"
                            v-model.number="form.category_id"
                            class="mt-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        >
                            <option :value="null">No category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.code }} - {{ category.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="txn-status">Status</label>
                        <input
                            id="txn-status"
                            v-model.number="form.status"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            type="number"
                            min="0"
                        />
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600" for="txn-name">Name</label>
                    <input
                        id="txn-name"
                        v-model="form.name"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. Shipment"
                        required
                        type="text"
                    />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600" for="txn-note">Note</label>
                    <textarea
                        id="txn-note"
                        v-model="form.note"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        rows="3"
                        placeholder="Optional note"
                    ></textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="txn-value">Value</label>
                        <input
                            id="txn-value"
                            v-model.number="form.value"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            type="number"
                            step="0.01"
                        />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="txn-price">Price</label>
                        <input
                            id="txn-price"
                            v-model.number="form.price"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            type="number"
                            step="0.01"
                        />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="txn-adjustment">Adjustment</label>
                        <input
                            id="txn-adjustment"
                            v-model.number="form.adjustment"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            type="number"
                            step="0.01"
                        />
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600" for="txn-final-price">Final price</label>
                    <input
                        id="txn-final-price"
                        v-model.number="form.final_price"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        type="number"
                        step="0.01"
                    />
                    <p class="mt-1 text-xs text-slate-400">You can set this manually (e.g. price + adjustment).</p>
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
                        {{ editing ? 'Update transaction' : 'Create transaction' }}
                    </button>
                </div>
            </form>
        </Modal>

        <Modal :open="deleteOpen" title="Delete transaction" eyebrow="Transactions" @close="closeDelete">
            <p class="text-sm text-slate-600">
                Are you sure you want to delete
                <span class="font-semibold text-slate-900">{{ selectedTransaction?.number }}</span>?
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

const transactions = ref([]);
const offices = ref([]);
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
const selectedTransaction = ref(null);
const openMenuId = ref(null);

const form = reactive({
    id: null,
    number: '',
    origin_office_id: null,
    destination_office_id: null,
    category_id: null,
    name: '',
    note: '',
    value: 0,
    price: 0,
    adjustment: 0,
    final_price: 0,
    status: 0,
});

const loaderConfig = useConfigLoaderStore();
const overlayConfig = computed(() => loaderConfig.overlay);

const fetchTransactions = async (page = currentPage.value) => {
    loading.value = true;
    error.value = '';
    try {
        const data = await apiGet(`/api/transactions?page=${page}`);
        transactions.value = Array.isArray(data) ? data : data.data || [];
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
            totalItems.value = transactions.value.length;
        }
    } catch (err) {
        error.value = 'Failed to load transactions.';
    } finally {
        loading.value = false;
    }
};

const fetchOffices = async () => {
    try {
        const data = await apiGet('/api/office-list');
        offices.value = Array.isArray(data) ? data : data?.data || [];
    } catch (e) {
        offices.value = [];
    }
};

const fetchCategories = async () => {
    try {
        const data = await apiGet('/api/category-list?section=transaction');
        categories.value = Array.isArray(data) ? data : data?.data || [];
    } catch (e) {
        categories.value = [];
    }
};

const refreshTransactions = async () => {
    if (refreshing.value) {
        return;
    }
    refreshing.value = true;
    await fetchTransactions(currentPage.value);
    refreshing.value = false;
};

const goToPage = async (page) => {
    if (loading.value || page < 1 || page > lastPage.value || page === currentPage.value) {
        return;
    }
    await fetchTransactions(page);
};

const toggleMenu = (transactionId) => {
    openMenuId.value = openMenuId.value === transactionId ? null : transactionId;
};

const closeMenu = () => {
    openMenuId.value = null;
};

const isBottomMenu = (index) => {
    return transactions.value.length - index <= 2;
};

const handleDocumentClick = (event) => {
    if (!openMenuId.value) {
        return;
    }
    const target = event.target;
    if (target && target.closest && target.closest('[data-txn-menu]')) {
        return;
    }
    closeMenu();
};

const resetForm = () => {
    form.id = null;
    form.number = '';
    form.origin_office_id = null;
    form.destination_office_id = null;
    form.category_id = null;
    form.name = '';
    form.note = '';
    form.value = 0;
    form.price = 0;
    form.adjustment = 0;
    form.final_price = 0;
    form.status = 0;
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
        origin_office_id: form.origin_office_id,
        destination_office_id: form.destination_office_id,
        category_id: form.category_id || null,
        name: form.name.trim(),
        note: (form.note || '').trim() || null,
        value: Number(form.value || 0),
        price: Number(form.price || 0),
        adjustment: Number(form.adjustment || 0),
        final_price: Number(form.final_price || 0),
        status: Number(form.status || 0),
    };

    try {
        if (editing.value) {
            await apiPatch(`/api/transactions/${form.id}`, payload);
            toaster('Transaction updated');
        } else {
            await apiPost('/api/transactions', payload);
            toaster('Transaction created');
        }

        formOpen.value = false;
        resetForm();
        await fetchTransactions();
    } catch (err) {
        error.value = 'Failed to save transaction.';
    } finally {
        saving.value = false;
    }
};

const startEdit = (txn) => {
    closeMenu();
    form.id = txn.id;
    form.number = txn.number || '';
    form.origin_office_id = txn.origin_office_id ?? null;
    form.destination_office_id = txn.destination_office_id ?? null;
    form.category_id = txn.category_id ?? null;
    form.name = txn.name || '';
    form.note = txn.note || '';
    form.value = txn.value ?? 0;
    form.price = txn.price ?? 0;
    form.adjustment = txn.adjustment ?? 0;
    form.final_price = txn.final_price ?? 0;
    form.status = txn.status ?? 0;
    editing.value = true;
    formOpen.value = true;
};

const openDelete = (txn) => {
    closeMenu();
    selectedTransaction.value = txn;
    deleteOpen.value = true;
};

const closeDelete = () => {
    deleteOpen.value = false;
    selectedTransaction.value = null;
};

const confirmDelete = async () => {
    if (!selectedTransaction.value) {
        return;
    }

    error.value = '';
    deleting.value = true;

    try {
        await apiDelete(`/api/transactions/${selectedTransaction.value.id}`);
        toaster('Transaction deleted');
        await fetchTransactions();
        if (editing.value && form.id === selectedTransaction.value.id) {
            resetForm();
        }
        closeDelete();
        closeMenu();
    } catch (err) {
        error.value = 'Failed to delete transaction.';
    } finally {
        deleting.value = false;
    }
};

onMounted(async () => {
    await Promise.all([fetchTransactions(), fetchOffices(), fetchCategories()]);
    document.addEventListener('click', handleDocumentClick);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
});
</script>

