<template>
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">
      <h4 class="fw-bold text-dark-blue mb-4">Nueva Notificación</h4>
      
      <div class="row">
        <div class="col-md-12 mb-4">
          <label class="form-label fw-bold">Buscar Expediente (Código o Nombre)</label>
          <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input 
              type="text" 
              class="form-control border-start-0" 
              v-model="busqueda" 
              @input="buscarExpediente"
              placeholder="Ej: IA-2026..."
            >
          </div>
          <ul v-if="resultados.length > 0" class="list-group position-absolute w-100 shadow-sm z-3">
            <li 
              v-for="exp in resultados" 
              :key="exp.id" 
              class="list-group-item list-group-item-action cursor-pointer"
              @click="seleccionarExpediente(exp)"
            >
              <strong>{{ exp.codigo }}</strong> - {{ exp.demandante }} vs {{ exp.demandado }}
            </li>
          </ul>
        </div>

        <div v-if="expedienteSeleccionado" class="col-md-12 mb-4">
          <div class="alert alert-secondary border-0 d-flex justify-content-between align-items-center">
            <div>
              <span class="badge bg-primary me-2">{{ expedienteSeleccionado.tipo }}</span>
              <strong>Materia:</strong> {{ expedienteSeleccionado.materia }}
            </div>
            <button class="btn btn-sm btn-close" @click="expedienteSeleccionado = null"></button>
          </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label fw-bold">Asunto de la Notificación</label>
                <input v-model="form.asunto" type="text" class="form-control" placeholder="Ej: Resolución N° 01 - Admisión">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Cuerpo del Mensaje</label>
                <textarea v-model="form.mensaje" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Adjuntar Archivos (PDF)</label>
                <input type="file" @change="handleFileUpload" multiple class="form-control" accept=".pdf">
            </div>
            <button class="btn btn-primary w-100 py-2 fw-bold" @click="enviarNotificacion" :disabled="!expedienteSeleccionado">
                DEPOSITAR EN CASILLA
            </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const busqueda = ref('');
const resultados = ref([]);
const expedienteSeleccionado = ref(null);
const form = reactive({ asunto: '', mensaje: '', archivos: [] });

const buscarExpediente = async () => {
  if (busqueda.value.length < 3) {
    resultados.value = [];
    return;
  }
  const res = await axios.get(`/expedientes/buscar?q=${busqueda.value}`);
  resultados.value = res.data;
};

const seleccionarExpediente = (exp) => {
  expedienteSeleccionado.value = exp;
  busqueda.value = exp.codigo;
  resultados.value = [];
};

const handleFileUpload = (event) => {
  form.archivos = Array.from(event.target.files);
};

const enviarNotificacion = async () => {
    // Aquí implementas el FormData y el envío que vimos anteriormente
    Swal.fire('¡Éxito!', 'Notificación enviada al expediente ' + expedienteSeleccionado.value.codigo, 'success');
};
</script>

<style scoped>
.cursor-pointer { cursor: pointer; }
.z-3 { z-index: 1050; }
</style>