<template>
    <div class="min-h-[calc(100vh-4rem)]">
        <div class="mx-auto flex min-h-screen max-w-md items-center px-6 py-12">
            <section class="w-full rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <h1 class="text-xl font-semibold text-slate-900">Sign in</h1>
                <p class="mt-1 text-sm text-slate-500">Use your email and password.</p>

                <form class="mt-6 space-y-4" @submit.prevent="submit">
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="email">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            placeholder="you@example.com"
                            required
                            type="email"
                            autocomplete="email"
                        />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-600" for="password">Password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            class="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-slate-400 focus:outline-none"
                            placeholder="••••••••"
                            required
                            type="password"
                            autocomplete="current-password"
                        />
                    </div>

                    <p v-if="error" class="text-sm text-rose-600">{{ error }}</p>

                    <button
                        class="inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-60"
                        type="submit"
                        :disabled="loading"
                    >
                        <span
                            v-if="loading"
                            class="h-4 w-4 animate-spin rounded-full border-2 border-white/60 border-t-white"
                        ></span>
                        Sign in
                    </button>
                </form>
            </section>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { apiPost } from '../../fetch_api.js';
import { AUTH_TOKEN_KEY } from '../../middleware/auth.js';

const router = useRouter();
const route = useRoute();

const loading = ref(false);
const error = ref('');
const form = reactive({
    email: '',
    password: '',
});

const submit = async () => {
    loading.value = true;
    error.value = '';
    try {
        const data = await apiPost('/api/login', {
            email: form.email.trim(),
            password: form.password,
        }, null);

        const token = data?.token;
        if (!token) {
            error.value = 'Login failed.';
            loading.value = false;
            return;
        }

        localStorage.setItem(AUTH_TOKEN_KEY, token);
        const redirect = route.query.redirect ? String(route.query.redirect) : '/dashboard';
        await router.replace(redirect);
    } catch (err) {
        error.value = 'Invalid email or password.';
    } finally {
        loading.value = false;
    }
};
</script>

