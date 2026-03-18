import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user')) || null,
        token: localStorage.getItem('token') || null,
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.user?.role === 'admin',
        isOperador: (state) => state.user?.role === 'operador',
    },
    actions: {
        async login(credentials) {
            const response = await axios.post('http://tu-api.test/api/login', credentials);
            this.token = response.data.access_token;
            this.user = response.data.user;

            localStorage.setItem('token', this.token);
            localStorage.setItem('user', JSON.stringify(this.user));

            // Configurar axios para futuras peticiones
            axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        },
        logout() {
            this.token = null;
            this.user = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            delete axios.defaults.headers.common['Authorization'];
        }
    }
});