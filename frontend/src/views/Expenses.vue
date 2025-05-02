<template>
  <div class="expense-page">
    <div class="header">
      <h1>Expenses</h1>
      <button 
        @click="toggleForm" 
        class="add-button"
      >
        {{ showForm ? 'Cancel' : 'Add Expense' }}
      </button>
    </div>

    <!-- Form (shown only when showForm is true or when editing) -->
    <ExpenseForm
      v-if="showForm || selectedExpense"
      :expense="selectedExpense"
      @submitted="handleSaveComplete"
      @cancel="handleCancel"
    />

    <!-- Expense List -->
    <ExpenseList 
      @editExpense="handleEditExpense" 
      class="list-section"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import ExpenseForm from '@/components/Expense/ExpenseForm.vue';
import ExpenseList from '@/components/Expense/ExpenseList.vue';

const showForm = ref(false);
const selectedExpense = ref(null);

function toggleForm() {
  showForm.value = !showForm.value;
  if (!showForm.value) {
    selectedExpense.value = null;
  }
}

function handleEditExpense(expense) {
  selectedExpense.value = expense;
  showForm.value = true;
}

function handleSaveComplete() {
  showForm.value = false;
  selectedExpense.value = null;
}

function handleCancel() {
  showForm.value = false;
  selectedExpense.value = null;
}
</script>

<style scoped>
.expense-page {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.add-button {
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s;
}

.add-button:hover {
  background-color: #0056b3;
}

.list-section {
  margin-top: 30px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>