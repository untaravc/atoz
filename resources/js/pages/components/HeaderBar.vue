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
            <div class="flex items-center gap-4 text-sm text-slate-600">
                <button class="rounded-full border border-slate-200 px-4 py-2 font-medium hover:bg-slate-50">Export</button>
                <button class="rounded-full bg-slate-900 px-4 py-2 font-semibold text-white">New report</button>
            </div>
        </div>
    </header>
</template>

<script setup>
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
</script>
