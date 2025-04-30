<template>
 
    <h1>Expenses</h1>
    <div class="expense-page">
      <!-- Form -->
      <ExpenseForm
        :expense="selectedExpense"
        @save-complete="handleSaveComplete"
      />

      <!-- Simple list of expenses -->
      <ExpenseList @editExpense="handleEditExpense" />
    </div>

</template>

<script setup>
import { ref } from 'vue';


import ExpenseForm from '@/components/Expense/ExpenseForm.vue';
import ExpenseList from '@/components/Expense/ExpenseList.vue';

const selectedExpense = ref(null);

function handleEditExpense(expense) {
  selectedExpense.value = expense;
}

function handleSaveComplete() {
  selectedExpense.value = null;
}
</script>
