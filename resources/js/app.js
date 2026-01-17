import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import { OhVueIcon } from 'oh-vue-icons';
import './icons';
import router from './route';
import DashboardRoot from './pages/DashboardRoot.vue';

createApp(DashboardRoot).component('VIcon', OhVueIcon).use(router).mount('#app');
