<template>
    <aside
        class="fixed left-0 top-0 z-50 flex h-full w-72 flex-col border-r border-slate-200 bg-white shadow-sm transition-transform lg:translate-x-0"
        :class="open ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex h-16 items-center justify-between border-b border-slate-200 px-6">
            <div class="flex items-center gap-2 text-lg font-semibold">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 text-white">A</span>
                <span>Admin Panel</span>
            </div>
            <button
                class="rounded-lg border border-slate-200 p-2 text-slate-600 lg:hidden"
                type="button"
                aria-label="Close sidebar"
                @click="$emit('close')"
            >
                <VIcon name="fa-times" class="h-4 w-4" />
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-6">
            <p class="px-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Main</p>
            <nav class="mt-4 space-y-1 text-sm font-medium">
                <RouterLink class="flex items-center gap-3 rounded-lg bg-primary px-3 py-2 text-white" to="/">
                    <VIcon name="fa-chart-pie" class="h-4 w-4" />
                    Dashboard
                </RouterLink>
                <a class="flex items-center gap-3 rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100" href="#">
                    <VIcon name="fa-file-alt" class="h-4 w-4" />
                    Analytics
                </a>
                <div class="rounded-lg border border-slate-200">
                    <button
                        class="flex w-full items-center justify-between px-3 py-2 text-slate-700"
                        @click="menuOpen.reports = !menuOpen.reports"
                    >
                        <span class="flex items-center gap-3">
                            <VIcon name="fa-file-alt" class="h-4 w-4" />
                            Reports
                        </span>
                        <span class="text-xs text-slate-500">{{ menuOpen.reports ? 'Hide' : 'Show' }}</span>
                    </button>
                    <div v-show="menuOpen.reports" class="space-y-1 border-t border-slate-200 px-3 py-2 text-slate-600">
                        <a class="block rounded-md px-2 py-1 hover:bg-slate-100" href="#">Revenue</a>
                        <a class="block rounded-md px-2 py-1 hover:bg-slate-100" href="#">Invoices</a>
                        <a class="block rounded-md px-2 py-1 hover:bg-slate-100" href="#">Expenses</a>
                    </div>
                </div>
                <div class="rounded-lg border border-slate-200">
                    <button
                        class="flex w-full items-center justify-between px-3 py-2 text-slate-700"
                        @click="menuOpen.management = !menuOpen.management"
                    >
                        <span class="flex items-center gap-3">
                            <VIcon name="fa-users" class="h-4 w-4" />
                            Management
                        </span>
                        <span class="text-xs text-slate-500">{{ menuOpen.management ? 'Hide' : 'Show' }}</span>
                    </button>
                    <div v-show="menuOpen.management" class="space-y-1 border-t border-slate-200 px-3 py-2 text-slate-600">
                        <a class="block rounded-md px-2 py-1 hover:bg-slate-100" href="#">Team</a>
                        <a class="block rounded-md px-2 py-1 hover:bg-slate-100" href="#">Projects</a>
                        <RouterLink class="block rounded-md px-2 py-1 hover:bg-slate-100" to="/roles">Roles</RouterLink>
                        <a class="block rounded-md px-2 py-1 hover:bg-slate-100" href="#">Settings</a>
                    </div>
                </div>
            </nav>
        </div>

        <div class="border-t border-slate-200 px-6 py-4 text-xs text-slate-500">
            Signed in as <span class="font-semibold text-slate-700">admin@example.com</span>
        </div>
    </aside>
</template>

<script setup>
import { reactive } from 'vue';

defineProps({
    open: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['close']);

const menuOpen = reactive({
    reports: true,
    management: false,
});
</script>
