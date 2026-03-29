<template>
    <div class="min-h-[calc(100vh-4rem)]">
        <div class="mx-auto flex min-h-screen max-w-lg items-center px-6 py-12">
            <section class="w-full rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <h1 class="text-xl font-semibold text-slate-900">401 - Unauthenticated</h1>
                <p class="mt-2 text-sm text-slate-600">
                    You need to sign in to access this page.
                </p>

                <div class="mt-6 flex flex-col gap-2 sm:flex-row">
                    <button
                        class="inline-flex cursor-pointer items-center justify-center rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary/90"
                        type="button"
                        @click="goLogin"
                    >
                        Go to login
                    </button>
                    <button
                        class="inline-flex cursor-pointer items-center justify-center rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        type="button"
                        @click="goDashboard"
                    >
                        Dashboard
                    </button>
                </div>
            </section>
        </div>
    </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router';

const router = useRouter();
const route = useRoute();

const goLogin = async () => {
    const redirect = route.query.redirect ? String(route.query.redirect) : '/dashboard';
    await router.push({ name: 'login', query: { redirect } });
};

const goDashboard = async () => {
    await router.push('/dashboard');
};
</script>

