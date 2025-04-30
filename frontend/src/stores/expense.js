import { defineStore } from 'pinia';
import api from '@/services/api';

export const useExpenseStore = defineStore('expense', {
  state: () => ({
    expenses: []
  }),

  getters: {
    getExpensesByGroupId: (state) => (groupId) =>
      state.expenses.filter(exp => exp.group_id === groupId)
  },

  actions: {
    async fetchExpenses() {
      try {
        console.log('Fetching expenses...');
        const res = await api.get('/api/expenses');
        console.log('Fetched expenses:', res.data.data.expenses);
        this.expenses = res.data.data || [];
      } catch (error) {
        console.error("Error fetching expenses:", error.response?.data || error.message);
      }
    },

    async addExpense(expense) {
      try {
        const payload = {
          expense_name: expense.expense_name,
          amount: expense.amount,
          expense_date: expense.expense_date,
          group_id: expense.group_id // updated to use group_id instead of group_name
        };

        const res = await api.post('/api/expenses', payload);
        this.expenses.push(res.data.data);
      } catch (error) {
        console.error("Error adding expense:", error.response?.data.data || error.message);
      }
    },

    async updateExpense(id, updatedExpense) {
      try {
        const payload = {
          expense_name: updatedExpense.expense_name,
          amount: updatedExpense.amount,
          expense_date: updatedExpense.expense_date,
          group_id: updatedExpense.group_id
        };

        const res = await api.put(`/api/expenses/${id}`, payload);
        this.expenses = this.expenses.map(exp => exp.id === id ? res.data.data : exp);
      } catch (error) {
        console.error("Error updating expense:", error.response?.data.data|| error.message);
      }
    },

    async deleteExpense(id) {
      try {
        await api.delete(`/api/expenses/${id}`);
        this.expenses = this.expenses.filter(exp => exp.id !== id);
      } catch (error) {
        console.error("Error deleting expense:", error.response?.data.data || error.message);
      }
    }
  }
});
