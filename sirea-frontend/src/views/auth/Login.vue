<template>
  <div class="login-container d-flex align-items-center justify-content-center">
    <div class="card shadow-lg border-0 login-card">
      <div class="card-body p-5">
        <div class="text-center mb-4">
          <img src="/logo_sin_fondo.png" alt="Logo Imperium" class="img-fluid mb-2" style="max-height: 70px;">
          <h4 class="fw-bold text-dark">SIREA</h4>
          <p class="text-muted small">Sistema de Casilla Electrónica</p>
        </div>

        <h5 class="text-center mb-4 text-secondary">Acceso a Casilla</h5>

        <form @submit.prevent="handleLogin">
          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase">N° de Documento (DNI/RUC)</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-person-badge text-muted"></i>
              </span>
              <input
                v-model="form.document_number"
                type="text"
                maxlength="11"
                class="form-control border-start-0 ps-0"
                placeholder="Ingrese su DNI o RUC"
                required
              >
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label small fw-bold text-uppercase">Contraseña</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-lock text-muted"></i>
              </span>
              <input
                v-model="form.password"
                type="password"
                class="form-control border-start-0 ps-0"
                placeholder="********"
                required
              >
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
            INGRESAR AL SISTEMA
          </button>
        </form>

        <div v-if="errorMessage" class="alert alert-danger mt-3 py-2 small border-0 text-center">
          <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ errorMessage }}
        </div>
      </div>

      <div class="card-footer bg-white border-0 text-center pb-4">
        <small class="text-muted">&copy; 2026 Imperium Arbitraje - SIREA</small>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const loading = ref(false);
const errorMessage = ref(null);

// Adecuado a la estructura de tu tabla 'users'
const form = reactive({
  document_number: '', 
  password: ''
});

const handleLogin = async () => {
  loading.value = true;
  errorMessage.value = null;

  try {
    // Apuntamos al endpoint usando la ruta relativa (la base URL está en src/axios.js)
    const response = await axios.post('/login', form);

    const { token, user } = response.data;

    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    // Redirección según rol o a la bandeja principal
    router.push({ name: 'casilla.bandeja' });
  } catch (error) {
    if (error.response?.status === 401 || error.response?.status === 422) {
      errorMessage.value = "Documento o contraseña incorrectos.";
    } else {
      errorMessage.value = "Error de conexión con el SIREA.";
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
/* Mantengo tu estilo con un ajuste sutil de color corporativo */
.login-container {
  width: 100%;
  min-height: 100vh;
  background: linear-gradient(135deg, #1e293b 0%, #003d99 100%);
}
.login-card {
  max-width: 420px;
  width: 100%;
  border-radius: 12px;
}
.btn-primary {
  background-color: #003d99;
  border: none;
}
.btn-primary:hover {
  background-color: #002b6b;
}
</style>