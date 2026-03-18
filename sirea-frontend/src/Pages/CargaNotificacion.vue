<template>
  <div class="card shadow">
    <div class="card-header bg-dark text-white">
      <h5 class="mb-0">Nueva Notificación Arbitral</h5>
    </div>
    <div class="card-body">
      <form @submit.prevent="enviar">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Expediente</label>
            <select v-model="form.expediente_id" class="form-select" required>
              <option v-for="exp in expedientes" :key="exp.id" :value="exp.id">
                {{ exp.numero }} - {{ exp.materia }}
              </option>
            </select>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Destinatario (Casilla)</label>
            <select v-model="form.casilla_id" class="form-select" required>
              <option v-for="cas in casillas" :key="cas.id" :value="cas.id">
                {{ cas.numero_casilla }} - {{ cas.user.name }}
              </option>
            </select>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Asunto / Sumilla</label>
          <input v-model="form.asunto" type="text" class="form-control" placeholder="Ej: Notificación de Laudo Arbitral">
        </div>

        <div class="mb-3">
          <label class="form-label">Documentos (PDF)</label>
          <input type="file" multiple @change="handleFiles" class="form-control" accept=".pdf">
        </div>

        <button type="submit" class="btn btn-primary w-100" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm"></span>
          Firmar y Depositar Notificación
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const form = ref({ expediente_id: '', casilla_id: '', asunto: '', archivos: [] });
const expedientes = ref([]);
const casillas = ref([]);
const loading = ref(false);

const handleFiles = (event) => {
  form.value.archivos = Array.from(event.target.files);
};

const enviar = async () => {
  loading.value = true;
  let data = new FormData();
  data.append('expediente_id', form.value.expediente_id);
  data.append('casilla_id', form.value.casilla_id);
  data.append('asunto', form.value.asunto);
  form.value.archivos.forEach(file => data.append('archivos[]', file));

  try {
    await axios.post('/api/notificaciones/carga', data);
    alert('Notificación enviada correctamente');
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};
</script>