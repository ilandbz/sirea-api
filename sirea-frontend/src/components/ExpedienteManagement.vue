<template>
  <div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark-blue">Registro de Expedientes</h5>
        <button class="btn btn-primary rounded-pill px-4" @click="mostrarModal = true">
          <i class="bi bi-plus-lg me-2"></i>Nuevo Expediente
        </button>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th class="ps-4">Código</th>
                <th>Tipo</th>
                <th>Materia</th>
                <th>Partes</th>
                <th>Estado</th>
                <th class="text-end pe-4">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="exp in expedientes" :key="exp.id">
                <td class="ps-4 fw-bold text-primary">{{ exp.codigo }}</td>
                <td>
                  <span :class="['badge rounded-pill', getBadgeTipo(exp.tipo)]">
                    {{ exp.tipo }}
                  </span>
                </td>
                <td>{{ exp.materia }}</td>
                <td>
                  <div v-if="exp.usuarios && exp.usuarios.length > 0">
                    <div
                      v-for="usuario in exp.usuarios"
                      :key="usuario.id"
                      class="mb-2 pb-1 border-bottom border-light"
                    >
                      <div class="d-flex align-items-center justify-content-between">
                        <div>
                          <span class="fw-bold text-dark small">{{ usuario.name }}</span>
                          <br>
                          <small class="text-muted">Doc: {{ usuario.document_number }}</small>
                          <button class="btn btn-sm text-danger border-0 p-0 ms-1" title="Quitar Parte" @click="quitarParte(exp.id, usuario.id)">
                            <i class="bi bi-x-circle-fill"></i>
                          </button>
                        </div>

                        <span
                          :class="['badge rounded-pill ms-2', getBadgePartClass(usuario.pivot.tipo_parte)]"
                        >
                          {{ usuario.pivot.tipo_parte }}
                          
                        </span>

                      </div>
                    </div>
                  </div>

                  <div v-else class="text-center py-2">
                    <span class="badge bg-warning-subtle text-warning rounded-pill small">
                      <i class="bi bi-exclamation-triangle me-1"></i> Sin usuarios vinculados
                    </span>
                  </div>
                </td>
                <td><span class="badge bg-light text-dark border">{{ exp.estado }}</span></td>
                <td class="text-end pe-4">
                  <button class="btn btn-sm btn-outline-secondary rounded-circle me-1"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-outline-danger rounded-circle" @click="eliminarExpediente(exp.id)">
                    <i class="bi bi-trash"></i>
                  </button>
                  <button class="btn btn-sm btn-outline-primary rounded-pill me-1" @click="prepararAsignacion(exp)">
                    <i class="bi bi-person-plus"></i> Asignar Partes
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-if="mostrarModal" class="modal-backdrop fade show"></div>
    <div v-if="mostrarModal" class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header">
            <h5 class="fw-bold">Registrar Solicitud / Expediente</h5>
            <button type="button" class="btn-close" @click="mostrarModal = false"></button>
          </div>
          <div class="modal-body p-4">
            <form @submit.prevent="guardarExpediente">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold small">Tipo de Proceso</label>
                  <select v-model="form.tipo" class="form-select bg-light border-0" required>
                    <option value="IA">Arbitraje (IA)</option>
                    <option value="JRD">Junta de Resolución (JRD)</option>
                    <option value="CONCILIACION">Conciliación</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold small">Código de Expediente</label>
                  <input v-model="form.codigo" type="text" class="form-control bg-light border-0" placeholder="Ej: IA-2026-0001" required>
                </div>
                <div class="col-md-12">
                  <label class="form-label fw-bold small">Materia</label>
                  <input v-model="form.materia" type="text" class="form-control bg-light border-0" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold small text-primary">Demandante / Solicitante</label>
                  <input v-model="form.demandante" type="text" class="form-control bg-light border-0" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold small text-danger">Demandado / Invitado</label>
                  <input v-model="form.demandado" type="text" class="form-control bg-light border-0" required>
                </div>
              </div>
              <div class="mt-4 text-end">
                <button type="button" class="btn btn-light rounded-pill me-2" @click="mostrarModal = false">Cancelar</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Guardar Expediente</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

<div class="modal fade" id="asignarUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header border-0 pb-0">
        <h5 class="fw-bold">Vincular Abogado al Expediente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <p class="text-muted small">Seleccione al usuario que recibirá las notificaciones de este caso.</p>
        
        <div class="mb-3">
          <label class="form-label fw-bold small">Buscar Usuario (Nombre o DNI)</label>
          <select v-model="asignacion.user_id" class="form-select bg-light border-0">
            <option v-for="u in listaUsuarios" :key="u.id" :value="u.id">
              {{ u.name }} ({{ u.document_number }})
            </option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold small">Tipo de Parte</label>
          <select v-model="asignacion.tipo_parte" class="form-select bg-light border-0">
            <option value="DEMANDANTE">DEMANDANTE / SOLICITANTE</option>
            <option value="DEMANDADO">DEMANDADO / INVITADO</option>
            <option value="ARBITRO">ÁRBITRO / TERCERO</option>
          </select>
        </div>

        <button class="btn btn-primary w-100 py-2 rounded-pill shadow-sm" @click="ejecutarVinculacion">
          VINCULAR A EXPEDIENTE
        </button>
      </div>
    </div>
  </div>
</div>

</template>
<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
// IMPORTANTE: Asegúrate de importar Modal de bootstrap si usas el objeto Modal
import { Modal } from 'bootstrap'; 

const expedientes = ref([]);
const mostrarModal = ref(false); // Modal para crear expediente
const form = reactive({ tipo: 'IA', codigo: '', materia: '', demandante: '', demandado: '' });

// Estados para la ASIGNACIÓN de usuarios
const asignacion = reactive({ user_id: '', tipo_parte: 'DEMANDANTE', expediente_id: null });
const listaUsuarios = ref([]);
let modalAsignarInstance = null; // Para controlar el modal de bootstrap

const fetchExpedientes = async () => {
  const res = await axios.get('/expedientes');
  expedientes.value = res.data;
};

// --- LÓGICA DE CREACIÓN DE EXPEDIENTE ---
const guardarExpediente = async () => {
  try {
    await axios.post('/expedientes', form);
    mostrarModal.value = false;
    Swal.fire('¡Éxito!', 'Expediente registrado correctamente', 'success');
    fetchExpedientes();
    Object.assign(form, { tipo: 'IA', codigo: '', materia: '', demandante: '', demandado: '' });
  } catch (error) {
    Swal.fire('Error', 'No se pudo registrar el expediente', 'error');
  }
};

// --- LÓGICA DE VINCULACIÓN (Aquí va lo que preguntaste) ---

const prepararAsignacion = async (exp) => {
  asignacion.expediente_id = exp.id;
  
  try {
    // 1. Cargamos usuarios que pueden ser notificados
    const res = await axios.get('/usuarios'); 
    listaUsuarios.value = res.data; // Filtra por rol si es necesario: .filter(u => u.role === 'usuario_final')
    
    // 2. Abrimos el modal de Bootstrap por ID
    const el = document.getElementById('asignarUserModal');
    modalAsignarInstance = new Modal(el);
    modalAsignarInstance.show();
  } catch (error) {
    Swal.fire('Error', 'No se pudo cargar la lista de usuarios', 'error');
  }
};

const ejecutarVinculacion = async () => {
  if (!asignacion.user_id) {
    Swal.fire('Atención', 'Seleccione un usuario', 'info');
    return;
  }

  try {
    await axios.post(`/expedientes/${asignacion.expediente_id}/vincular-usuario`, {
      user_id: asignacion.user_id,
      tipo_parte: asignacion.tipo_parte
    });

    await fetchExpedientes(); // <- recarga la tabla con las partes actualizadas

    modalAsignarInstance.hide();

    Swal.fire(
      '¡Vínculo Exitoso!',
      'El usuario fue vinculado correctamente al expediente.',
      'success'
    );

    asignacion.user_id = '';
    asignacion.tipo_parte = 'DEMANDANTE';
    asignacion.expediente_id = null;
  } catch (error) {
    console.error(error);
    Swal.fire(
      'Error',
      'El usuario ya podría estar vinculado o hubo un fallo en el servidor.',
      'error'
    );
  }
};

const getBadgeTipo = (tipo) => {
  const types = { 'IA': 'bg-primary', 'JRD': 'bg-warning text-dark', 'CONCILIACION': 'bg-info text-white' };
  return types[tipo] || 'bg-secondary';
};

const getBadgePartClass = (tipoParte) => {
  if (tipoParte === 'DEMANDANTE') return 'bg-success-subtle text-success border border-success';
  if (tipoParte === 'DEMANDADO') return 'bg-danger-subtle text-danger border border-danger';
  if (tipoParte === 'ARBITRO') return 'bg-secondary-subtle text-secondary border border-secondary';
  return 'bg-light text-dark border';
};

const eliminarExpediente = async (id) => {
  const result = await Swal.fire({
    title: '¿Eliminar Expediente?',
    text: "Esta acción no se puede deshacer.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar'
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/expedientes/${id}`);
      Swal.fire('Eliminado', 'Expediente borrado.', 'success');
      fetchExpedientes(); // Recargar lista
    } catch (e) {
      Swal.fire('Error', 'No se pudo eliminar el expediente.', 'error');
    }
  }
};

const quitarParte = async (expId, userId) => {
  try {
    await axios.post(`/expedientes/${expId}/desvincular-usuario`, { user_id: userId });
    Swal.fire('Desvinculado', 'El usuario ya no pertenece al expediente.', 'success');
    fetchExpedientes();
  } catch (e) {
    Swal.fire('Error', 'No se pudo desvincular.', 'error');
  }
};

onMounted(fetchExpedientes);
</script>

<style scoped>
/* ... otros estilos ... */
.bg-success-subtle { background-color: #d1e7dd; }
.bg-danger-subtle { background-color: #f8d7da; }
.bg-warning-subtle { background-color: #fff3cd; }
.bg-secondary-subtle { background-color: #e2e3e5; }
.border-light { border-color: #f1f1f1 !important; }
</style>