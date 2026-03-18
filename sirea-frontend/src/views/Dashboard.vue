<!-- <template>
  <div class="d-flex bg-light vh-100">
    <div class="bg-dark text-white p-3 shadow" style="width: 280px;">
      <div class="text-center mb-4">
        <h4 class="fw-bold text-primary">SIREA</h4>
        <small class="text-muted">Imperium Arbitraje</small>
      </div>
      <hr>
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
          <a href="#" class="nav-link active">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
          </a>
        </li>
        <li class="nav-item mb-2">
          <a href="#" class="nav-link text-white">
            <i class="bi bi-folder me-2"></i> Expedientes
          </a>
        </li>
        <li class="nav-item mb-2">
          <a href="#" class="nav-link text-white">
            <i class="bi bi-envelope me-2"></i> Mi Casilla
          </a>
        </li>
        <li v-if="user?.role === 'operador'" class="nav-item mb-2">
          <a href="#" class="nav-link text-white text-warning">
            <i class="bi bi-plus-circle me-2"></i> Nueva Notificación
          </a>
        </li>
      </ul>
      <hr>
      <div class="dropdown">
        <button @click="handleLogout" class="btn btn-danger w-100 shadow-sm">
          <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
        </button>
      </div>
    </div>

    <div class="flex-grow-1 p-4 overflow-auto">
      <header class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h2 class="h4">Bienvenido, {{ user?.name }}</h2>
        <span class="badge bg-primary px-3 py-2">Rol: {{ user?.role }}</span>
      </header>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
              <div class="bg-primary text-white rounded-circle p-3 me-3">
                <i class="bi bi-envelope-open fs-4"></i>
              </div>
              <div>
                <h6 class="text-muted mb-0">Notificaciones</h6>
                <p class="h4 mb-0 fw-bold">12</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
              <div class="bg-success text-white rounded-circle p-3 me-3">
                <i class="bi bi-journal-text fs-4"></i>
              </div>
              <div>
                <h6 class="text-muted mb-0">Expedientes Activos</h6>
                <p class="h4 mb-0 fw-bold">4</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center border-start border-4 border-warning ps-3">
              <div>
                <h6 class="text-muted mb-0">Último Depósito</h6>
                <p class="small mb-0">Hace 2 horas</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-5">
        <div class="alert alert-info border-0 shadow-sm">
          <i class="bi bi-info-circle-fill me-2"></i>
          Para enviar una notificación, asegúrese de tener el archivo PDF del cargo listo y firmado.
        </div>
      </div>
    </div>
  </div>
</template> -->

<template>
  <div> <h2 class="h4 mb-4">Resumen de Actividad</h2>
    <div class="row g-4">
       </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const user = ref(null);

onMounted(() => {
  // Recuperar datos del usuario del LocalStorage
  const userData = localStorage.getItem('user');
  if (userData) {
    user.value = JSON.parse(userData);
  } else {
    router.push('/login');
  }
});

const handleLogout = () => {
    // 1. Borramos los datos locales
    localStorage.removeItem('token');
    localStorage.removeItem('user');

    // 2. Limpiamos el token de las futuras peticiones de Axios
    delete axios.defaults.headers.common['Authorization'];

    // 3. Opcional: Avisar al backend (si quieres invalidar el token en DB)
    // axios.post('/logout'); 

    // 4. Redirigir al login
    router.push({ name: 'login' });
};
</script>

<style scoped>
.nav-link:hover {
  background-color: rgba(255,255,255,0.1);
}
.card {
  border-radius: 10px;
}
</style>