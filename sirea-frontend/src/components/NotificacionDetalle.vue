<template>
  <div class="container py-5">
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <div v-else-if="notificacion" class="card shadow-sm border-0 rounded-4">
      <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
        <div>
          <button @click="$router.back()" class="btn btn-link text-decoration-none p-0 mb-2">
            <i class="bi bi-arrow-left"></i> Volver a la bandeja
          </button>
          <h4 class="fw-bold text-dark-blue mb-0">{{ notificacion.asunto }}</h4>
          <span class="badge bg-light text-dark border mt-2">Expediente: {{ notificacion.expediente?.numero }}</span>
        </div>
        <div class="text-end text-muted small">
          <p class="mb-0">Depositado: {{ notificacion.created_at }}</p>
          <p class="mb-0 text-success" v-if="notificacion.fecha_lectura">
             <i class="bi bi-check-all"></i> Leído el: {{ notificacion.fecha_lectura }}
          </p>
        </div>
      </div>

      <div class="card-body p-4">
        <div class="mb-5 p-4 bg-light rounded-3 shadow-inner">
          <p style="white-space: pre-wrap;" class="lead text-dark">{{ notificacion.cuerpo }}</p>
        </div>

        <h5 class="fw-bold mb-3"><i class="bi bi-paperclip me-2"></i>Documentos Adjuntos</h5>
        <div class="list-group">
          <div v-for="file in notificacion.adjuntos" :key="file.id" 
               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
            <div class="d-flex align-items-center">
              <i class="bi bi-file-earmark-pdf-fill text-danger fs-3 me-3"></i>
              <div>
                <p class="mb-0 fw-bold">{{ file.nombre_archivo }}</p>
                <small class="text-muted">Documento Oficial Firmado Digitalmente</small>
              </div>
            </div>
            <button 
              @click="descargarArchivo(file)" 
              class="btn btn-primary rounded-pill px-4 shadow-sm">
              <i class="bi bi-download me-1"></i> Descargar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const notificacion = ref(null);
const loading = ref(true);
const descargarArchivo = async (file) => {
  try {
    const response = await axios.get(`/storage/${file.ruta_storage}`, {
      responseType: 'blob' // IMPORTANTE
    });

    // Crear URL del archivo
    const url = window.URL.createObjectURL(new Blob([response.data]));

    // Crear link temporal
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', file.nombre_archivo); // nombre del archivo
    document.body.appendChild(link);

    // Descargar
    link.click();

    // Limpiar
    link.remove();
    window.URL.revokeObjectURL(url);

  } catch (error) {
    console.error('Error al descargar archivo', error);
  }
};
const fetchDetalle = async () => {
  try {
    const response = await axios.get(`/notificaciones/${route.params.id}`);
    notificacion.value = response.data;
  } catch (error) {
    console.error("Error al cargar detalle", error);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchDetalle);
</script>

<style scoped>
.text-dark-blue { color: #002e6e; }
.bg-light { background-color: #f8f9fa !important; }
.shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
</style>