<template>
    <div class="expense-view">
      <div class="header">
        <h1>Expenses</h1>
        <button 
          @click="showForm = !showForm" 
          class="add-button"
        >
          {{ showForm ? 'Cancel' : 'Add Expense' }}
        </button>
      </div>
  
      <!-- Expense Form (conditionally shown) -->
      <ExpenseForm 
        v-if="showForm" 
        :expense="editingExpense"
        @submitted="handleFormSubmit"
        @group-changed="handleGroupChange"
        class="form-section"
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
  import ExpenseForm from '@/components/ExpenseForm.vue';
  import ExpenseList from '@/components/ExpenseList.vue';
  
  const showForm = ref(false);
  const editingExpense = ref(null);
  
  function handleEditExpense(expense) {
    editingExpense.value = expense;
    showForm.value = true;
  }
  
  function handleFormSubmit() {
    showForm.value = false;
    editingExpense.value = null;
  }
  
  function handleGroupChange(groupId) {
    // Handle group change if needed
  }
  </script>
  
  <style scoped>
  .expense-view {
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
  
  .form-section {
    margin-bottom: 30px;
  }
  
  .list-section {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  </style>