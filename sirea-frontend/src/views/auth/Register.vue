<template>
  <div class="login-wrapper">
    <!-- Top Right Action Buttons -->
    <div class="position-absolute top-0 end-0 p-4 d-flex gap-3 z-3">
      <a href="/manual.pdf" target="_blank" class="btn btn-white rounded-circle shadow d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;" title="Manual">
        <i class="bi bi-book fs-5" style="color: #003d99;"></i>
      </a>
      <a href="#" class="btn btn-white rounded-circle shadow d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;" title="Videos tutoriales">
        <i class="bi bi-camera-video fs-5" style="color: #003d99;"></i>
      </a>
      <a href="#" class="btn btn-white rounded-circle shadow d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;" title="Ayuda">
        <i class="bi bi-question-circle fs-5" style="color: #003d99;"></i>
      </a>
    </div>

    <div class="login-container d-flex align-items-center justify-content-center">
      <div class="card shadow-lg border-0 login-card overflow-hidden">
        <div class="row g-0 h-100">
          
          <div class="col-md-5 d-none d-md-flex flex-column justify-content-center align-items-center text-white left-panel position-relative p-5">
            <!-- Logo -->
            <div class="logo-container mb-5 z-1">
                <img src="/logo_sin_fondo.png" alt="Logo SIREA" class="img-fluid">
            </div>

            <!-- Texto -->
            <div class="text-center z-1">
                <h2 class="fw-light mb-2">Registro en</h2>
                <h1 class="fw-bold display-3 mb-4" style="letter-spacing:2px;">SIREA</h1>
                <p class="fs-5 opacity-75 mb-5">
                    Cree su cuenta para acceder al sistema de Casilla Electrónica
                </p>

                <div class="d-inline-flex align-items-center bg-white text-dark px-3 py-2 rounded-pill shadow">
                    🇵🇪 <span class="ms-2 fw-bold">PE</span>
                </div>
            </div>

            <div class="pattern-overlay"></div>
          </div>

          <!-- Right panel (Form) -->
          <div class="col-md-7 bg-white">
            <div class="card-body p-sm-5 p-4 h-100 d-flex flex-column justify-content-center position-relative">
              
              <div class="text-center mb-4 mt-3">
                <h4 class="fw-bold text-dark mb-1">Registro de Usuario</h4>
                <p class="text-muted small">Complete sus datos para registrarse</p>
              </div>

              <form @submit.prevent="handleRegister" class="w-100 mx-auto" style="max-width: 420px;">
                
                <div class="mb-3">
                  <label class="form-label small fw-bold text-muted">Nombre Completo / Razón Social</label>
                  <input
                    v-model="form.name"
                    type="text"
                    class="form-control rounded-pill px-4 py-2 custom-input shadow-none"
                    placeholder="Ingrese su nombre o razón social"
                    required
                  >
                </div>

                <div class="mb-3">
                  <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
                  <input
                    v-model="form.email"
                    type="email"
                    class="form-control rounded-pill px-4 py-2 custom-input shadow-none"
                    placeholder="ejemplo@correo.com"
                    required
                  >
                </div>

                <div class="row mb-3">
                  <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">Tipo de Doc.</label>
                    <select v-model="form.document_type" class="form-select rounded-pill px-3 py-2 custom-input shadow-none" required>
                      <option value="DNI">DNI</option>
                      <option value="RUC">RUC</option>
                    </select>
                  </div>
                  <div class="col-md-7">
                    <label class="form-label small fw-bold text-muted">Número de Documento</label>
                    <input
                      v-model="form.document_number"
                      type="text"
                      maxlength="11"
                      class="form-control rounded-pill px-4 py-2 custom-input shadow-none"
                      placeholder="DNI o RUC"
                      required
                    >
                  </div>
                </div>

                <div class="mb-4">
                  <label class="form-label small fw-bold text-muted">Contraseña</label>
                  <div class="input-group custom-input-group rounded-pill overflow-hidden">
                    <input
                      v-model="form.password"
                      :type="showPassword ? 'text' : 'password'"
                      class="form-control border-0 bg-transparent px-4 py-2 shadow-none"
                      placeholder="Mínimo 6 caracteres"
                      required
                    >
                    <span 
                      class="input-group-text bg-transparent border-0 pe-3" 
                      @click="showPassword = !showPassword"
                      style="cursor: pointer;"
                    >
                      <i class="bi" :class="showPassword ? 'bi-eye-slash text-muted' : 'bi-eye text-muted'"></i>
                    </span>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm rounded-pill mb-3" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                  Crear Cuenta
                </button>
                
                <div v-if="errorMessage" class="alert alert-danger py-2 small border-0 text-center rounded-pill">
                  <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ errorMessage }}
                </div>

                <div class="text-center mb-5">
                  <span class="small text-muted">¿Ya tienes una cuenta? </span>
                  <router-link to="/login" class="small fw-bold text-decoration-none" style="color: #003d99;">Iniciar Sesión</router-link>
                </div>
              </form>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';

const router = useRouter();
const loading = ref(false);
const errorMessage = ref(null);
const showPassword = ref(false);

const form = reactive({
  name: '',
  email: '',
  document_type: 'DNI',
  document_number: '', 
  password: ''
});

const handleRegister = async () => {
  loading.value = true;
  errorMessage.value = null;

  try {
    const response = await axios.post('/register', form);

    const { token, user } = response.data;

    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    Swal.fire({
      icon: 'success',
      title: '¡Registro Exitoso!',
      text: 'Su cuenta ha sido creada y se le ha asignado una casilla.',
      timer: 2000,
      showConfirmButton: false
    });

    router.push({ name: 'casilla.bandeja' });
  } catch (error) {
    if (error.response?.data?.errors) {
       const errors = error.response.data.errors;
       errorMessage.value = Object.values(errors).flat().join(' ');
    } else {
       errorMessage.value = error.response?.data?.message || "Error al crear la cuenta.";
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.login-wrapper {
  width: 100%;
  min-height: 100vh;
  background: linear-gradient(135deg, #1e293b 0%, #003d99 100%);
  position: relative;
}

.login-container {
  position: relative;
  z-index: 2;
  min-height: 100vh;
  padding: 2rem 1rem;
}

.login-card {
  max-width: 950px;
  width: 100%;
  border-radius: 24px;
  min-height: 600px;
}

.left-panel {
  background-color: #003d99;
  background: linear-gradient(145deg, #0047b3 0%, #002b6b 100%);
}

.pattern-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
  background-size: 20px 20px;
  opacity: 0.5;
  pointer-events: none;
}

.custom-input, .custom-input-group {
  border: 1px solid #e2e8f0;
  background-color: #ffffff;
  transition: all 0.2s ease-in-out;
}

.custom-input:focus, .custom-input-group:focus-within {
  border-color: #003d99;
  box-shadow: 0 0 0 0.25rem rgba(0, 61, 153, 0.1);
}

.btn-primary {
  background-color: #003d99;
  border: none;
  transition: all 0.3s;
}

.btn-primary:hover {
  background-color: #002b6b;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 61, 153, 0.3) !important;
}

.logo-container{
    width:260px;
    height:170px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#fff;
    border-radius:26px;
    padding:22px;
    box-shadow: 0 20px 40px rgba(0,0,0,.20);
    z-index:2;
}

.logo-container img{
    max-width:100%;
    max-height:100%;
    object-fit:contain;
}

@media (max-width: 767px) {
  .login-card {
    border-radius: 16px;
  }
}

.btn-white {
  background-color: #ffffff;
  border: none;
  transition: all 0.3s;
}

.btn-white:hover {
  background-color: #f8fafc;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}
</style>
