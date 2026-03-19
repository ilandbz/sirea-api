<template>
  <div class="inbox-wrapper bg-light min-vh-100 p-4">
    <div class="container-fluid">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 class="fw-bold text-dark-blue mb-0">
            <i class="bi bi-mailbox2 me-2"></i>Mi Casilla Electrónica
          </h3>
          <p class="text-muted small">Historial de notificaciones recibidas en el SIREA</p>
        </div>
        <button @click="fetchNotifications" class="btn btn-outline-primary btn-sm rounded-pill">
          <i class="bi bi-arrow-clockwise me-1"></i> Actualizar
        </button>
      </div>

      <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-dark-blue text-white">
              <tr>
                <th class="ps-4 py-3">Estado</th>
                <th>Expediente</th>
                <th>Asunto / Sumilla</th>
                <th>Fecha de Depósito</th>
                <th>Documentos</th>
                <th class="text-center pe-4">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="6" class="text-center py-5">
                  <div class="spinner-border text-primary" role="status"></div>
                  <p class="mt-2 text-muted">Consultando servidor...</p>
                </td>
              </tr>

              <tr v-if="!loading && notifications.length === 0">
                <td colspan="6" class="text-center py-5">
                  <i class="bi bi-envelope-x fs-1 text-muted"></i>
                  <p class="mt-2 text-muted">No tiene notificaciones pendientes en su casilla.</p>
                </td>
              </tr>

              <tr v-for="nota in notifications" :key="nota.id" :class="{'bg-unread': !nota.fecha_lectura}">
                <td class="ps-4">
                  <i v-if="!nota.fecha_lectura" class="bi bi-envelope-fill text-warning fs-5" title="No leído"></i>
                  <i v-else class="bi bi-envelope-open text-muted fs-5" title="Leído"></i>
                </td>
                <td>
                  <span class="fw-bold text-dark">{{ nota.expediente?.numero || 'EXP-000' }}</span>
                </td>
                <td>
                  <div class="d-flex flex-column">
                    <span class="fw-semibold">{{ nota.asunto }}</span>
                    <span class="text-muted small text-truncate" style="max-width: 300px;">{{ nota.cuerpo }}</span>
                  </div>
                </td>
                <td>
                  <div class="small">
                    <i class="bi bi-calendar3 me-1"></i>{{ formatDate(nota.created_at) }}<br>
                    <i class="bi bi-clock me-1"></i>{{ formatTime(nota.created_at) }}
                  </div>
                </td>
                <td>
                  <span class="badge bg-light text-dark border">
                    <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                    {{ nota.adjuntos?.length || 0 }} archivo(s)
                  </span>
                </td>
                <td class="text-center pe-4">
                  <button @click="verDetalle(nota)" class="btn btn-dark-blue btn-sm px-3 rounded-pill shadow-sm">
                    Abrir Notificación
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const notifications = ref([]);
const loading = ref(false);

const fetchNotifications = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/notificaciones');
    notifications.value = response.data;
  } catch (error) {
    console.error("Error al cargar notificaciones", error);
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '---';
  const date = new Date(dateString);
  return date.toLocaleDateString('es-PE', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};


const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });
};

const verDetalle = (nota) => {
  // Aquí redirigiremos a una vista detalle o abriremos un modal
  router.push({ name: 'casilla.detalle', params: { id: nota.id } });
};

onMounted(fetchNotifications);
</script>

<style scoped>
.text-dark-blue {
  color: #002e6e;
}

.bg-dark-blue {
  background-color: #002e6e;
}

.bg-unread {
  background-color: rgba(0, 46, 110, 0.03); /* Un azul muy tenue para filas no leídas */
  border-left: 4px solid #fbc02d; /* Línea amarilla lateral de advertencia */
}

.table th {
  font-weight: 500;
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
}

.btn-dark-blue {
  background-color: #002e6e;
  color: white;
  border: none;
  transition: all 0.3s ease;
}

.btn-dark-blue:hover {
  background-color: #001a41;
  transform: translateY(-1px);
}

.rounded-4 {
  border-radius: 1rem !important;
}
</style>