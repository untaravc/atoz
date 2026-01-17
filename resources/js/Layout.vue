<template>
    <div class="min-h-screen bg-slate-50 text-slate-900">
        <div
            class="fixed inset-0 z-40 bg-slate-900/40 transition-opacity lg:hidden"
            :class="sidebarOpen ? 'opacity-100' : 'pointer-events-none opacity-0'"
            @click="sidebarOpen = false"
        ></div>

        <SidebarNav :open="sidebarOpen" @close="sidebarOpen = false" />

        <div class="flex min-h-screen flex-col lg:pl-72">
            <HeaderBar @toggle-sidebar="sidebarOpen = true" />
            <main class="flex-1 px-6 py-8">
                <RouterView />
            </main>
            <FooterBar />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import HeaderBar from './pages/components/HeaderBar.vue';
import SidebarNav from './pages/components/SidebarNav.vue';
import FooterBar from './pages/components/FooterBar.vue';

const sidebarOpen = ref(false);
</script>
