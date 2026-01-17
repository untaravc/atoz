import { createRouter, createWebHistory } from 'vue-router';
import Layout from './Layout.vue';
import RolesIndex from './pages/roles/RolesIndex.vue';
import DashboardIndex from './pages/dashboard/DashboardIndex.vue';
import UsersIndex from './pages/users/UsersIndex.vue';

const routes = [
    {
        path: '/',
        name: 'layout',
        component: Layout,
        children: [
            {
                path: '/dashboard',
                name: 'dashboard',
                component: DashboardIndex,
            },
            {
                path: '/roles',
                name: 'roles',
                component: RolesIndex,
            },
            {
                path: '/users',
                name: 'users',
                component: UsersIndex,
            },
        ]
    },

];

const router = createRouter({
    history: createWebHistory('/bo'),
    routes,
});

export default router;
