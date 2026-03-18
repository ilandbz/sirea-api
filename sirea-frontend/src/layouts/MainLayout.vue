<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import axios from 'axios';

const router = useRouter();
const user = ref(JSON.parse(localStorage.getItem('user')));

const handleLogout = () => {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Deseas cerrar sesión?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            delete axios.defaults.headers.common['Authorization'];
            router.push({ name: 'login' });
        }
    });
};
</script>


<template>
  <div class="d-flex bg-light vh-100">
    <div class="bg-dark text-white p-3 shadow" style="width: 280px; min-width: 280px;">
      <div class="text-center mb-4 mt-2">
        <img src="/logo_sin_fondo.png" alt="Logo" class="img-fluid mb-2" style="max-height: 50px; filter: brightness(0) invert(1);">
        <h4 class="fw-bold text-primary">SIREA</h4>
      </div>
      <hr>
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
          <router-link :to="{ name: 'dashboard' }" class="nav-link text-white" active-class="active bg-primary">
            <i class="bi bi-speedometer2 me-2"></i> Inicio
          </router-link>
        </li>
        <li class="nav-item mb-2">
          <router-link :to="{ name: 'casilla.bandeja' }" class="nav-link text-white" active-class="active bg-primary">
            <i class="bi bi-envelope me-2"></i> Mi Casilla
          </router-link>
        </li>
        <li v-if="user?.role === 'admin' || user?.role === 'operador'" class="nav-item mb-2">
          <router-link :to="{ name: 'casilla.notificar' }" class="nav-link text-white text-warning" active-class="active bg-primary">
            <i class="bi bi-plus-circle me-2"></i> Nueva Notificación
          </router-link>
        </li>
        <li v-if="user?.role === 'admin'" class="nav-item mb-2">
          <router-link :to="{ name: 'usuarios.index' }" class="nav-link text-white" active-class="active bg-primary">
            <i class="bi bi-people me-2"></i> Usuarios
          </router-link>
        </li>
        <li v-if="user?.role === 'admin'" class="nav-item mb-2">
          <router-link :to="{ name: 'expedientes.index' }" class="nav-link text-white" active-class="active bg-primary">
            <i class="bi bi-folder me-2"></i> Expedientes
          </router-link>
        </li>
      </ul>
      <hr>
      <button @click="handleLogout" class="btn btn-outline-danger w-100 border-0 text-start ps-3">
        <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
      </button>
    </div>

    <div class="flex-grow-1 p-0 overflow-auto">
      <header class="bg-white shadow-sm p-3 d-flex justify-content-between align-items-center mb-0">
        <span class="text-muted small">Imperium Arbitraje - Sistema de Casilla</span>
        <div class="d-flex align-items-center">
          <span class="me-3 fw-bold small">{{ user?.name }}</span>
          <span class="badge bg-secondary font-monospace">{{ user?.role }}</span>
        </div>
      </header>
      
      <main class="p-4">
        <router-view />
      </main>
    </div>
  </div>
</template>



<style scoped>
.nav-link { transition: 0.3s; border-radius: 8px; }
.nav-link:hover:not(.active) { background-color: rgba(255,255,255,0.1); }
</style>