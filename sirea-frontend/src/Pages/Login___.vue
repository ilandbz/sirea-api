<template>
  <div class="login-page bg-light vh-100 d-flex align-items-center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
          
          <div class="text-center mb-4">
            <img src="https://imperium.org.pe/wp-content/uploads/2023/04/logo-imperium.png" 
                 alt="Imperium Logo" class="img-fluid" style="max-height: 80px;">
            <h4 class="mt-3 fw-bold text-secondary">SIREA</h4>
            <p class="text-muted small">Sistema de Casilla Electrónica de Arbitraje</p>
          </div>

          <div class="card shadow-sm border-0">
            <div class="card-body p-4">
              <h5 class="card-title mb-4 text-center">Iniciar Sesión</h5>
              
              <form @submit.prevent="handleLogin">
                <div class="mb-3">
                  <label class="form-label">Correo Electrónico</label>
                  <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                    <input v-model="form.email" type="email" class="form-control" placeholder="nombre@ejemplo.com" required>
                  </div>
                </div>

                <div class="mb-4">
                  <label class="form-label">Contraseña</label>
                  <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                    <input v-model="form.password" type="password" class="form-control" placeholder="********" required>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                  Ingresar al Sistema
                </button>
              </form>

              <div v-if="error" class="alert alert-danger mt-3 py-2 small">
                {{ error }}
              </div>
            </div>
          </div>
          
          <p class="text-center mt-4 text-muted small">
            &copy; 2024 Imperium - Centro de Arbitraje
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const auth = useAuthStore();
const router = useRouter();

const form = ref({ email: '', password: '' });
const loading = ref(false);
const error = ref(null);

const handleLogin = async () => {
  loading.value = true;
  error.value = null;
  try {
    await auth.login(form.value);
    router.push({ name: 'dashboard' }); // Redirigir al entrar
  } catch (err) {
    error.value = err.response?.data?.message || 'Error de conexión';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-page {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}
.card {
  border-radius: 12px;
}
</style>