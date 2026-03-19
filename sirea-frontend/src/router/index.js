import { createRouter, createWebHistory } from 'vue-router';
import Login from '../views/auth/Login.vue';
import Dashboard from '../views/Dashboard.vue';
import Inbox from '../components/Inbox.vue'; // Verifica que la ruta sea correcta
import CargaNotificacion from '../components/CargaNotificacion.vue';
import UserManagement from '../components/UserManagement.vue';
import NotificacionDetalle from '../components/NotificacionDetalle.vue';
import ExpedienteManagement from '../components/ExpedienteManagement.vue';
import MainLayout from '../layouts/MainLayout.vue';
const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: '/',
        component: MainLayout, // El padre siempre muestra el sidebar
        meta: { requiresAuth: true },
        children: [
            {
                path: 'dashboard', // queda como /dashboard
                name: 'dashboard',
                component: Dashboard,
            },
            {
                path: 'bandeja', // queda como /bandeja
                name: 'casilla.bandeja',
                component: Inbox,
            },
            {
                path: 'notificacion/:id',
                name: 'casilla.detalle',
                component: NotificacionDetalle,
            },
            {
                path: 'notificar',
                name: 'casilla.notificar',
                component: CargaNotificacion,
            },
            {
                path: 'usuarios',
                name: 'usuarios.index',
                component: UserManagement,
            },
            {
                path: 'expedientes',
                name: 'expedientes.index',
                component: ExpedienteManagement
            }
        ]
    },
    // {
    //     path: '/:pathMatch(.*)*',
    //     redirect: '/login'
    // }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from) => {
    const isAuthenticated = !!localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user'));

    if (to.meta.requiresAuth && !isAuthenticated) {
        return { name: 'login' }
    }
    // Validación opcional de roles
    else if (to.meta.role && user?.role !== to.meta.role) {
        alert("No tienes permisos para acceder aquí");
        return { name: 'dashboard' };
    }
    else {
        return true;
    }
});

export default router;