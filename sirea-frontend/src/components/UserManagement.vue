<template>
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="text-dark-blue fw-bold"><i class="bi bi-people-fill me-2"></i>Gestión de Usuarios y Casillas</h4>
      <button class="btn btn-success rounded-pill px-4" @click="openModal">
        <i class="bi bi-person-plus-fill me-2"></i>Nuevo Usuario
      </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light text-secondary small text-uppercase">
            <tr>
              <th class="ps-4">Usuario / Razón Social</th>
              <th>Documento</th>
              <th>Tipo</th>
              <th>N° Casilla</th>
              <th>Estado</th>
              <th class="text-end pe-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td class="ps-4">
                <div class="fw-bold text-dark">{{ user.name }}</div>
                <div class="text-muted small">{{ user.email }}</div>
              </td>
              <td><code>{{ user.document_number }}</code></td>
              <td><span class="badge bg-light text-dark border">{{ user.document_type }}</span></td>
              <td>
                <span class="text-primary fw-bold">{{ user.casilla?.numero_casilla || 'SIN ASIGNAR' }}</span>
              </td>
              <td>
                <span :class="['badge rounded-pill', user.is_active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger']">
                  {{ user.is_active ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
              <td class="text-end pe-4">
                <button class="btn btn-sm btn-outline-primary rounded-circle me-1" title="Ver Historial">
                  <i class="bi bi-clock-history"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-circle" title="Editar">
                  <i class="bi bi-pencil"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content border-0 shadow rounded-4">
          <div class="modal-header border-0 pb-0">
            <h5 class="fw-bold">Registrar Nuevo Usuario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="saveUser">
              <div class="mb-3">
                <label class="form-label small fw-bold">Nombre Completo</label>
                <input v-model="form.name" type="text" class="form-control bg-light border-0" required>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label small fw-bold">Tipo Doc.</label>
                  <select v-model="form.document_type" class="form-select bg-light border-0">
                    <option value="DNI">DNI</option>
                    <option value="RUC">RUC</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label small fw-bold">N° Documento</label>
                  <input v-model="form.document_number" type="text" class="form-control bg-light border-0" required>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label small fw-bold">Correo Electrónico</label>
                <input v-model="form.email" type="email" class="form-control bg-light border-0" required>
              </div>
              <div class="mb-3">
                <label class="form-label small fw-bold">Rol en el Sistema</label>
                <select v-model="form.role" class="form-select bg-light border-0">
                  <option value="usuario_final">Abogado / Parte</option>
                  <option value="operador">Operador (Imperium)</option>
                  <option value="admin">Administrador</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow-sm mt-3" :disabled="saving">
                <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
                Generar Casilla y Guardar
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

const users = ref([]);
const saving = ref(false);
let modalInstance = null;

const form = reactive({
  name: '',
  email: '',
  document_number: '',
  document_type: 'DNI',
  role: 'usuario_final'
});

const fetchUsers = async () => {
  try {
    const response = await axios.get('/usuarios');
    users.value = response.data;
  } catch (error) {
    console.error("Error al cargar usuarios", error);
  }
};

const openModal = () => {
  modalInstance = new Modal(document.getElementById('userModal'));
  modalInstance.show();
};

const saveUser = async () => {
  saving.value = true;
  try {
    await axios.post('/usuarios', form);
    modalInstance.hide();
    Swal.fire('¡Éxito!', 'Usuario creado y casilla asignada.', 'success');
    fetchUsers(); // Recargar lista
    // Limpiar form
    Object.assign(form, { name: '', email: '', document_number: '', document_type: 'DNI', role: 'usuario_final' });
  } catch (error) {
    Swal.fire('Error', error.response?.data?.message || 'No se pudo crear el usuario', 'error');
  } finally {
    saving.value = false;
  }
};

onMounted(fetchUsers);
</script>

<style scoped>
.text-dark-blue { color: #002e6e; }
.bg-success-subtle { background-color: #d1e7dd; }
.bg-danger-subtle { background-color: #f8d7da; }
</style>