import { createRouter, createWebHistory } from 'vue-router';
import Layout from './Layout.vue';
import RolesIndex from './pages/roles/RolesIndex.vue';
import DashboardIndex from './pages/dashboard/DashboardIndex.vue';
import UsersIndex from './pages/users/UsersIndex.vue';
import MenusIndex from './pages/menus/MenusIndex.vue';
import MenuRole from './pages/menus/MenuRole.vue';
import OfficesIndex from './pages/offices/OfficesIndex.vue';
import CategoriesIndex from './pages/categories/CategoriesIndex.vue';
import Login from './pages/auth/Login.vue';
import Unauthorized from './pages/auth/401.vue';
import { authGuard } from './middleware/auth.js';
import TransactionsIndex from './pages/transactions/TransactionsIndex.vue';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
    },
    {
        path: '/401',
        name: 'unauthorized',
        component: Unauthorized,
    },
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
            {
                path: '/menus',
                name: 'menus',
                component: MenusIndex,
            },
            {
                path: '/menu-role',
                name: 'menu-role',
                component: MenuRole,
            },
            {
                path: '/offices',
                name: 'offices',
                component: OfficesIndex,
            },
            {
                path: '/categories',
                name: 'categories',
                component: CategoriesIndex,
            },
            {
                path: '/transactions',
                name: 'transactions',
                component: TransactionsIndex,
            },
        ]
    },

];

const router = createRouter({
    history: createWebHistory('/bo'),
    routes,
});

router.beforeEach(authGuard);

export default router;
