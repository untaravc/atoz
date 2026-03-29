<template>
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="flex h-16 items-center justify-between px-6">
            <div class="flex items-center gap-3">
                <button
                    class="rounded-lg border border-slate-200 p-2 text-slate-600 lg:hidden"
                    type="button"
                    aria-label="Open sidebar"
                    @click="$emit('toggle-sidebar')"
                >
                    <VIcon name="fa-bars" class="h-4 w-4" />
                </button>
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ eyebrow }}</p>
                    <p class="text-lg font-semibold text-slate-900">{{ title }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 text-sm text-slate-600">
                <div v-if="profileLoading" class="text-xs text-slate-400">Loading...</div>

                <div v-if="profile" class="relative" data-profile-menu>
                    <button
                        class="inline-flex cursor-pointer items-center gap-3 rounded-full border border-slate-200 px-3 py-2 hover:bg-slate-50"
                        type="button"
                        :aria-expanded="profileMenuOpen"
                        @click="profileMenuOpen = !profileMenuOpen"
                        data-profile-menu
                    >
                        <span class="hidden text-right sm:block">
                            <span class="block text-xs font-semibold text-slate-700">{{ profile.name }}</span>
                            <span class="block text-[11px] text-slate-400">{{ profile.role || 'No role' }}</span>
                        </span>
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-white">
                            {{ initials(profile.name) }}
                        </span>
                    </button>

                    <div
                        v-if="profileMenuOpen"
                        class="absolute right-0 top-12 z-20 w-64 rounded-xl border border-slate-200 bg-white p-3 text-left text-xs shadow-lg"
                        data-profile-menu
                    >
                        <div class="flex items-start gap-3">
                            <span
                                class="inline-flex h-10 w-10 flex-none items-center justify-center rounded-full bg-slate-900 text-white"
                            >
                                {{ initials(profile.name) }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ profile.name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ profile.email }}</p>
                                <p class="mt-1 text-[11px] font-semibold text-slate-400">{{ profile.role || 'No role' }}</p>
                            </div>
                        </div>

                        <div class="mt-3 border-t border-slate-100 pt-3">
                            <button
                                class="inline-flex w-full cursor-pointer items-center justify-center rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-500 disabled:opacity-60"
                                type="button"
                                :disabled="logoutLoading"
                                @click="logout"
                            >
                                <span
                                    v-if="logoutLoading"
                                    class="mr-2 h-3 w-3 animate-spin rounded-full border-2 border-white/60 border-t-white"
                                ></span>
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { apiGet, apiPost } from '../../fetch_api.js';
import { clearAuthToken } from '../../middleware/auth.js';

defineProps({
    eyebrow: {
        type: String,
        default: 'Dashboard',
    },
    title: {
        type: String,
        default: 'Overview',
    },
});

defineEmits(['toggle-sidebar']);

const router = useRouter();

const profile = ref(null);
const profileLoading = ref(false);
const logoutLoading = ref(false);
const profileMenuOpen = ref(false);

const initials = (name) => {
    const text = (name || '').trim();
    if (!text) {
        return 'U';
    }
    const parts = text.split(/\s+/).filter(Boolean);
    const first = parts[0]?.[0] || 'U';
    const last = parts.length > 1 ? parts[parts.length - 1][0] : '';
    return (first + last).toUpperCase();
};

const fetchProfile = async () => {
    profileLoading.value = true;
    try {
        const data = await apiGet('/api/profile');
        profile.value = data?.user ?? null;
    } catch (e) {
        profile.value = null;
    } finally {
        profileLoading.value = false;
    }
};

const logout = async () => {
    if (logoutLoading.value) {
        return;
    }
    logoutLoading.value = true;
    try {
        await apiPost('/api/logout', {});
    } catch (e) {
        // ignore
    } finally {
        clearAuthToken();
        profileMenuOpen.value = false;
        logoutLoading.value = false;
        router.replace({ name: 'login' });
    }
};

const handleDocumentClick = (event) => {
    if (!profileMenuOpen.value) {
        return;
    }
    const target = event.target;
    if (target && target.closest && target.closest('[data-profile-menu]')) {
        return;
    }
    profileMenuOpen.value = false;
};

onMounted(() => {
    fetchProfile();
    document.addEventListener('click', handleDocumentClick);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleDocumentClick);
});
</script>
