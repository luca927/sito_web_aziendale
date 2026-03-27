import './bootstrap';
import { createApp } from 'vue';
import AppClienti from './components/App.vue';
import ToastContainer from './components/ToastContainer.vue';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
const app = createApp({});

app.component('toast-container', ToastContainer);
app.mount('#app');