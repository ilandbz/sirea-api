<template>
  <div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body p-4">
        <h4 class="fw-bold text-dark-blue mb-4">Nueva Notificación Arbitral</h4>
        
        <div class="row g-4">
          <div class="col-md-6">
            <label class="form-label fw-bold small">1. Buscar Expediente</label>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
              <input 
                type="text" 
                class="form-control border-start-0 ps-0 bg-light" 
                v-model="query" 
                @input="buscar"
                placeholder="Escriba el código del expediente..."
              >
            </div>
            <ul v-if="resultados.length > 0" class="list-group position-absolute shadow-lg z-3 w-50">
              <li v-for="exp in resultados" :key="exp.id" 
                  class="list-group-item list-group-item-action cursor-pointer"
                  @click="seleccionar(exp)">
                <span class="badge bg-primary me-2">{{ exp.tipo }}</span> <strong>{{ exp.codigo }}</strong>
              </li>
            </ul>
          </div>

          <div class="col-md-6" v-if="expedienteSeleccionado">
            <div class="p-3 rounded-4 border bg-light">
              <h6 class="fw-bold small text-uppercase mb-3 text-secondary">Casillas que serán notificadas:</h6>
              <div v-for="u in expedienteSeleccionado.usuarios" :key="u.id" class="d-flex align-items-center mb-2">
                <i class="bi bi-mailbox2 text-primary me-2"></i>
                <div class="small">
                  <span class="fw-bold">{{ u.name }}</span>
                  <span class="badge bg-dark-subtle text-dark ms-2">{{ u.pivot.tipo_parte }}</span>
                </div>
              </div>
              <div v-if="!expedienteSeleccionado.usuarios?.length" class="alert alert-warning py-2 small mb-0">
                <i class="bi bi-exclamation-triangle me-1"></i> Este expediente no tiene usuarios vinculados.
              </div>
            </div>
          </div>

          <hr v-if="expedienteSeleccionado">

          <div class="col-md-12" v-if="expedienteSeleccionado && expedienteSeleccionado.usuarios?.length > 0">
            <div class="mb-3">
              <label class="form-label fw-bold small">Asunto / Sumilla</label>
              <input v-model="form.asunto" type="text" class="form-control" placeholder="Ej: Resolución N° 05 - Programación de Audiencia">
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold small">Cuerpo del Mensaje</label>
              <textarea v-model="form.mensaje" class="form-control" rows="4" placeholder="Escriba el detalle de la notificación..."></textarea>
            </div>
            <div class="mb-4">
              <label class="form-label fw-bold small">Documentos Adjuntos (PDF)</label>
              <input type="file" @change="handleFiles" multiple class="form-control" accept=".pdf">
              <div class="form-text">Puede seleccionar varios archivos PDF a la vez.</div>
            </div>
            <button class="btn btn-primary btn-lg w-100 rounded-pill shadow" @click="enviar" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              <i class="bi bi-send-check me-2"></i> DEPOSITAR EN CASILLAS ELECTRÓNICAS
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const query = ref('');
const resultados = ref([]);
const expedienteSeleccionado = ref(null);
const loading = ref(false);

const form = reactive({
  asunto: '',
  mensaje: '',
  archivos: []
});

const buscar = async () => {
  if (query.value.length < 2) { resultados.value = []; return; }
  const res = await axios.get(`/expedientes/buscar?q=${query.value}`);
  resultados.value = res.data;
};



const handleFiles = (e) => {
  form.archivos = Array.from(e.target.files);
};

const seleccionar = (exp) => {
  expedienteSeleccionado.value = exp;
  query.value = exp.codigo;
  resultados.value = [];
};

const enviar = async () => {
  if (!form.asunto || form.archivos.length === 0) {
    Swal.fire('Atención', 'Complete el asunto y adjunte al menos un PDF', 'warning');
    return;
  }

  if (!expedienteSeleccionado.value) {
    Swal.fire('Atención', 'Seleccione un expediente', 'warning');
    return;
  }

  loading.value = true;

  const data = new FormData();
  data.append('expediente_id', expedienteSeleccionado.value.id);
  data.append('asunto', form.asunto);
  data.append('mensaje', form.mensaje);

  form.archivos.forEach(file => {
    data.append('archivos[]', file);
  });

  try {
    await axios.post('/notificaciones', data);

    Swal.fire('¡Éxito!', 'La notificación ha sido depositada correctamente.', 'success');

    expedienteSeleccionado.value = null;
    query.value = '';
    Object.assign(form, { asunto: '', mensaje: '', archivos: [] });
  } catch (error) {
    console.error(error.response?.data);
    Swal.fire('Error', error.response?.data?.message || 'Error al enviar', 'error');
  } finally {
    loading.value = false;
  }
};

</script>