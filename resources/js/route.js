import { createRouter, createWebHistory } from 'vue-router';
import DashboardRoot from './pages/DashboardRoot.vue';

const routes = [
    {
        path: '/',
        name: 'dashboard',
        component: DashboardRoot,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
