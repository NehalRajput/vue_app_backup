import axios from 'axios';

const api = axios.create({
  baseURL: 'http://127.0.0.1:8000', // Use /api for Laravel routes
  headers: {
    'Content-Type': 'application/json',
    // Accept: 'application/json',
  },
});

// Automatically attach token (if present)
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;
