import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import { OhVueIcon } from 'oh-vue-icons';
import './icons';
import router from './route';
import App from './App.vue';

createApp(App).component('VIcon', OhVueIcon).use(router).mount('#app');
