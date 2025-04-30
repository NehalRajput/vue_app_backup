import { defineStore } from 'pinia';
import api from '@/services/api';

export const useGroupStore = defineStore('group', {
  state: () => ({
    groups: []
  }),

  actions: {
    async fetchGroups() {
      try {
        const res = await api.get('/api/groups');
        console.log(res)
        this.groups = res.data.data|| [];
      } catch (error) {
        console.error("Error fetching groups:", error.response?.data || error.message);
      }
    },

    async addGroup(group) {
      try {
        const res = await api.post('/api/groups', group);
        this.groups.push(res.data.data);
      } catch (error) {
        console.error("Error adding group:", error.response?.data || error.message);
      }
    },

    async updateGroup(id, group) {
      try {
        const res = await api.put(`/api/groups/${id}`, group);
        this.groups = this.groups.map(g => g.id === id ? res.data.data : g);
      } catch (error) {
        console.error("Error updating group:", error.response?.data.data || error.message);
      }
    },

    async deleteGroup(id) {
      try {
        await api.delete(`/api/groups/${id}`);
        this.groups = this.groups.filter(g => g.id !== id);
      } catch (error) {
        console.error("Error deleting group:", error.response?.data.data || error.message);
      }
    }
  }
});
