import { createApp } from 'vue'
import './style.css'
import './axios' // Importar la configuración de Axios
import App from './App.vue'
import router from './router';
import { createPinia } from 'pinia'

import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import 'bootstrap-icons/font/bootstrap-icons.css';



const app = createApp(App)

app.use(createPinia()) // Para el estado global
app.use(router)        // Para la navegación
app.mount('#app')