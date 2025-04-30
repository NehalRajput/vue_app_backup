import { defineStore } from 'pinia';
import api from '@/services/api';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),

  actions: {
    async login(form) {
      try {
        const response = await api.post('/api/login', form);
        this.token = response.data.data.token;
        localStorage.setItem('token', this.token);
        await this.fetchUser();
        return true;
      } catch (error) {
        console.error("Login failed:", error.response?.data || error.message);
        return false;
      }
    },

    async register(form) {
      try {
        const response = await api.post('/api/register', form);
        this.token = response.data.data.token;
        localStorage.setItem('token', this.token);
        await this.fetchUser();
        return true;
      } catch (error) {
        console.error("Registration failed:", error.response?.data || error.message);
        return false;
      }
    },

    async fetchUser() {
      try {
        const response = await api.get('/api/user', {
          headers: { Authorization: `Bearer ${this.token}` }
        });
        this.user = response.data;
      } catch (error) {
        console.error("Failed to fetch user:", error.response?.data || error.message);
      }
    },

    logout() {
      this.user = null;
      this.token = null;
      localStorage.removeItem('token');
    }
  },
});
