import './bootstrap';
import '../css/app.css';
import { createApp } from 'vue';
import { OhVueIcon } from 'oh-vue-icons';
import './icons';
import { createPinia } from 'pinia';
import router from './route';
import App from './App.vue';

const app = createApp(App);
app.use(createPinia());

app.use(router);
app.component('VIcon', OhVueIcon);
app.mount('#app');
