<template>
    <div>
        <div class="flex flex-col gap-6">
            <section class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Offices</h2>
                    <div class="flex items-center gap-2">
                        <button
                            class="inline-flex cursor-pointer items-center gap-2 rounded-full border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                            type="button"
                            :disabled="refreshing"
                            @click="refreshOffices"
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
                            Create office
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
                                <th class="px-3 py-2">Email</th>
                                <th class="px-3 py-2">Phone</th>
                                <th class="px-3 py-2">City</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(office, index) in offices" :key="office.id">
                                <td class="px-3 py-3 font-medium text-slate-900">{{ office.code || '-' }}</td>
                                <td class="px-3 py-3 font-medium text-slate-900">{{ office.name }}</td>
                                <td class="px-3 py-3">{{ office.email || '-' }}</td>
                                <td class="px-3 py-3">{{ office.phone || '-' }}</td>
                                <td class="px-3 py-3">{{ office.city || '-' }}</td>
                                <td class="px-3 py-3">
                                    <label class="inline-flex items-center gap-2">
                                        <input
                                            class="h-4 w-4"
                                            type="checkbox"
                                            :checked="Boolean(office.status)"
                                            :disabled="saving || Boolean(togglingStatus[office.id])"
                                            @change="toggleOfficeStatus(office, $event.target.checked)"
                                        />
                                        <span class="text-xs font-semibold text-slate-600">
                                            {{ office.status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </label>
                                </td>
                                <td class="px-3 py-3 text-right">
                                    <div class="relative inline-flex justify-end" data-office-menu>
                                        <button
                                            class="inline-flex h-8 w-8 cursor-pointer items-center justify-center rounded-full border border-slate-200 text-slate-500 hover:bg-slate-50"
                                            type="button"
                                            :aria-expanded="openMenuId === office.id"
                                            aria-label="Open actions"
                                            @click="toggleMenu(office.id)"
                                            data-office-menu
                                        >
                                            <VIcon name="fa-ellipsis-v" class="h-4 w-4" />
                                        </button>
                                        <div
                                            v-if="openMenuId === office.id"
                                            class="absolute right-0 z-10 w-32 rounded-lg border border-slate-200 bg-white py-1 text-left text-xs shadow-lg"
                                            :class="isBottomMenu(index) ? 'bottom-10' : 'top-10'"
                                            data-office-menu
                                        >
                                            <button
                                                class="block w-full px-3 py-2 text-left text-slate-600 hover:bg-slate-50"
                                                type="button"
                                                @click="startEdit(office)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                class="block w-full px-3 py-2 text-left text-rose-600 hover:bg-rose-50"
                                                type="button"
                                                @click="openDelete(office)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-if="offices.length === 0 && !loading" class="mt-4 text-sm text-slate-500">No offices found.</p>
                    <p v-if="loading" class="mt-4 text-sm text-slate-500">Loading offices...</p>
                </div>

                <PaginationNav
                    :current-page="currentPage"
                    :last-page="lastPage"
                    :total="totalItems || offices.length"
                    :from="offices.length ? (currentPage - 1) * perPage + 1 : 0"
                    :to="Math.min(currentPage * perPage, totalItems || offices.length)"
                    :loading="loading"
                    @page-change="goToPage"
                />
            </section>
        </div>

        <Modal
            :open="formOpen"
            :title="editing ? 'Edit office' : 'Create office'"
            eyebrow="Offices"
            @close="closeForm"
        >
            <form class="space-y-4" @submit.prevent="handleSubmit">
                <div>
                    <label class="text-sm font-medium text-slate-600" for="office-code">Code</label>
                    <input
                        id="office-code"
                        v-model="form.code"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. JKT-HQ"
                        required
                        type="text"
                    />
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-600" for="office-name">Name</label>
                    <input
                        id="office-name"
                        v-model="form.name"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. Jakarta HQ"
                        required
                        type="text"
                    />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600" for="office-email">Email</label>
                    <input
                        id="office-email"
                        v-model="form.email"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="office@example.com"
                        type="email"
                    />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600" for="office-phone">Phone</label>
                    <input
                        id="office-phone"
                        v-model="form.phone"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. +62 21 1234 5678"
                        type="text"
                    />
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600" for="office-address">Address</label>
                    <textarea
                        id="office-address"
                        v-model="form.address"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="Street address"
                        rows="3"
                    ></textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="office-province">Province ID</label>
                        <input
                            id="office-province"
                            v-model.number="form.province_id"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            placeholder="e.g. 31"
                            type="number"
                        />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="office-city">City</label>
                        <input
                            id="office-city"
                            v-model="form.city"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            placeholder="e.g. Jakarta"
                            type="text"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="office-latitude">Latitude</label>
                        <input
                            id="office-latitude"
                            v-model="form.latitude"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            placeholder="-6.2000000"
                            type="number"
                            step="0.0000001"
                        />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="office-longitude">Longitude</label>
                        <input
                            id="office-longitude"
                            v-model="form.longitude"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            placeholder="106.8166667"
                            type="number"
                            step="0.0000001"
                        />
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600" for="office-image">Image</label>
                    <input
                        id="office-image"
                        v-model="form.image"
                        class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                        placeholder="e.g. /storage/offices/hq.png"
                        type="text"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <input id="office-status" v-model="form.status" class="h-4 w-4" type="checkbox" />
                    <label class="text-sm text-slate-600" for="office-status">Active</label>
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
                        {{ editing ? 'Update office' : 'Create office' }}
                    </button>
                </div>
            </form>
        </Modal>

        <Modal :open="deleteOpen" title="Delete office" eyebrow="Offices" @close="closeDelete">
            <p class="text-sm text-slate-600">
                Are you sure you want to delete
                <span class="font-semibold text-slate-900">{{ selectedOffice?.name }}</span>?
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

const offices = ref([]);
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
const selectedOffice = ref(null);
const openMenuId = ref(null);
const togglingStatus = ref({});
const form = reactive({
    id: null,
    code: '',
    name: '',
    address: '',
    phone: '',
    latitude: '',
    longitude: '',
    image: '',
    status: true,
    email: '',
    province_id: null,
    city: '',
});

const loaderConfig = useConfigLoaderStore();
const overlayConfig = computed(() => loaderConfig.overlay);

const fetchOffices = async (page = currentPage.value) => {
    loading.value = true;
    error.value = '';
    try {
        const data = await apiGet(`/api/offices?page=${page}`);
        offices.value = Array.isArray(data) ? data : data.data || [];
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
            totalItems.value = offices.value.length;
        }
    } catch (err) {
        error.value = 'Failed to load offices.';
    } finally {
        loading.value = false;
    }
};

const refreshOffices = async () => {
    if (refreshing.value) {
        return;
    }
    refreshing.value = true;
    await fetchOffices(currentPage.value);
    refreshing.value = false;
};

const goToPage = async (page) => {
    if (loading.value || page < 1 || page > lastPage.value || page === currentPage.value) {
        return;
    }
    await fetchOffices(page);
};

const toggleMenu = (officeId) => {
    openMenuId.value = openMenuId.value === officeId ? null : officeId;
};

const closeMenu = () => {
    openMenuId.value = null;
};

const isBottomMenu = (index) => {
    return offices.value.length - index <= 2;
};

const handleDocumentClick = (event) => {
    if (!openMenuId.value) {
        return;
    }
    const target = event.target;
    if (target && target.closest && target.closest('[data-office-menu]')) {
        return;
    }
    closeMenu();
};

const resetForm = () => {
    form.id = null;
    form.code = '';
    form.name = '';
    form.address = '';
    form.phone = '';
    form.latitude = '';
    form.longitude = '';
    form.image = '';
    form.status = true;
    form.email = '';
    form.province_id = null;
    form.city = '';
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

const normalizeOptionalNumber = (value) => {
    if (value === null || value === undefined || value === '') {
        return null;
    }
    const n = Number(value);
    return Number.isFinite(n) ? n : null;
};

const handleSubmit = async () => {
    error.value = '';
    saving.value = true;

    const payload = {
        code: form.code.trim(),
        name: form.name.trim(),
        address: normalizeOptionalText(form.address),
        phone: normalizeOptionalText(form.phone),
        latitude: normalizeOptionalNumber(form.latitude),
        longitude: normalizeOptionalNumber(form.longitude),
        image: normalizeOptionalText(form.image),
        status: Boolean(form.status),
        email: normalizeOptionalText(form.email),
        province_id: normalizeOptionalNumber(form.province_id),
        city: normalizeOptionalText(form.city),
    };

    try {
        if (editing.value) {
            await apiPatch(`/api/offices/${form.id}`, payload);
            toaster('Office updated');
        } else {
            await apiPost('/api/offices', payload);
            toaster('Office created');
        }

        formOpen.value = false;
        resetForm();
        await fetchOffices();
    } catch (err) {
        error.value = 'Failed to save office.';
    } finally {
        saving.value = false;
    }
};

const startEdit = (office) => {
    closeMenu();
    form.id = office.id;
    form.code = office.code || '';
    form.name = office.name || '';
    form.address = office.address || '';
    form.phone = office.phone || '';
    form.latitude = office.latitude ?? '';
    form.longitude = office.longitude ?? '';
    form.image = office.image || '';
    form.status = Boolean(office.status);
    form.email = office.email || '';
    form.province_id = office.province_id ?? null;
    form.city = office.city || '';
    editing.value = true;
    formOpen.value = true;
};

const toggleOfficeStatus = async (office, nextValue) => {
    if (!office || !office.id) {
        return;
    }
    togglingStatus.value = { ...togglingStatus.value, [office.id]: true };
    try {
        await apiPatch(`/api/offices/${office.id}`, { status: Boolean(nextValue) });
        office.status = Boolean(nextValue);
        toaster('Office status updated');
    } catch (err) {
        error.value = 'Failed to update office status.';
    } finally {
        togglingStatus.value = { ...togglingStatus.value, [office.id]: false };
    }
};

const openDelete = (office) => {
    closeMenu();
    selectedOffice.value = office;
    deleteOpen.value = true;
};

const closeDelete = () => {
    deleteOpen.value = false;
    selectedOffice.value = null;
};

const confirmDelete = async () => {
    if (!selectedOffice.value) {
        return;
    }

    error.value = '';
    deleting.value = true;

    try {
        await apiDelete(`/api/offices/${selectedOffice.value.id}`);
        toaster('Office deleted');
        await fetchOffices();
        if (editing.value && form.id === selectedOffice.value.id) {
            resetForm();
        }
        closeDelete();
        closeMenu();
    } catch (err) {
        error.value = 'Failed to delete office.';
    } finally {
        deleting.value = false;
    }
};

onMounted(() => {
    fetchOffices();
    document.addEventListener('click', handleDocumentClick);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
});
</script>
