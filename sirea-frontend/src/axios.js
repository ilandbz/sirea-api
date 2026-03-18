import axios from 'axios';

axios.defaults.baseURL = 'http://sirea-api.test/api'; // Cambia por tu URL local

// Si tenemos un token guardado, lo inyectamos en las cabeceras automáticamente
const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}