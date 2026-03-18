<template>
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">
      <h4 class="fw-bold text-dark-blue mb-4">Nueva Notificación</h4>
      
      <form @submit.prevent="handleSubmit">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Tipo de Proceso</label>
            <select v-model="form.tipo_expediente" class="form-select" @change="fetchExpedientesPorTipo">
              <option value="IA">Expediente de Arbitraje (IA)</option>
              <option value="JRD">Junta de Resolución de Disputas (JRD)</option>
              <option value="CONCILIACION">Conciliación</option>
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Seleccionar Expediente</label>
            <select v-model="form.expediente_id" class="form-select" :disabled="!form.tipo_expediente">
              <option v-for="exp in expedientesFiltrados" :key="exp.id" :value="exp.id">
                {{ exp.numero }} - {{ exp.demandante }}
              </option>
            </select>
          </div>
        </div>

        </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import Swal from 'sweetalert2';
import axios from 'axios';

const form = reactive({
  tipo_expediente: 'IA',
  expediente_id: '',
  asunto: '',
  archivos: []
});

const expedientesFiltrados = ref([]);

const fetchExpedientesPorTipo = async () => {
  try {
    const response = await axios.get(`/expedientes?tipo=${form.tipo_expediente}`);
    expedientesFiltrados.value = response.data;
  } catch (error) {
    Swal.fire('Error', 'No se pudieron cargar los expedientes', 'error');
  }
};

const handleSubmit = async () => {
  // Lógica de carga con SweetAlert de "Procesando..."
  Swal.fire({
    title: 'Enviando Notificación',
    text: 'Por favor espere mientras se firman los documentos...',
    allowOutsideClick: false,
    didOpen: () => { Swal.showLoading(); }
  });

  try {
    // ... tu lógica de axios.post ...
    Swal.fire('¡Éxito!', 'La notificación ha sido depositada en la casilla.', 'success');
  } catch (err) {
    Swal.fire('Error', 'Hubo un problema al subir los archivos.', 'error');
  }
};
</script>