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
                  <small class="d-block"><strong>D:</strong> {{ exp.demandante }}</small>
                  <small class="d-block"><strong>R:</strong> {{ exp.demandado }}</small>
                </td>
                <td><span class="badge bg-light text-dark border">{{ exp.estado }}</span></td>
                <td class="text-end pe-4">
                  <button class="btn btn-sm btn-outline-secondary rounded-circle me-1"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button>
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
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const expedientes = ref([]);
const mostrarModal = ref(false);
const form = reactive({ tipo: 'IA', codigo: '', materia: '', demandante: '', demandado: '' });

const fetchExpedientes = async () => {
  const res = await axios.get('/expedientes');
  expedientes.value = res.data;
};

const guardarExpediente = async () => {
  try {
    await axios.post('/expedientes', form);
    mostrarModal.value = false;
    Swal.fire('¡Éxito!', 'Expediente registrado correctamente', 'success');
    fetchExpedientes();
    // Resetear form
    Object.assign(form, { tipo: 'IA', codigo: '', materia: '', demandante: '', demandado: '' });
  } catch (error) {
    Swal.fire('Error', 'No se pudo registrar el expediente', 'error');
  }
};

const getBadgeTipo = (tipo) => {
  if (tipo === 'IA') return 'bg-primary';
  if (tipo === 'JRD') return 'bg-warning text-dark';
  return 'bg-info text-white';
};

onMounted(fetchExpedientes);
</script>