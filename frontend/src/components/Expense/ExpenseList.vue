<template>
  <div class="expense-list">
    <DataTable :items="expenses" :columns="columns">
      <!-- Amount Column -->
      <template #amount="{ value }">
        <span class="amount">₹{{ value }}</span>
      </template>
      
      <!-- Group Column -->
      <template #group_id="{ item }">
        <span class="group-tag">
          {{ getGroupName(item.group_id) }}
        </span>
      </template>
      
      <!-- Actions Column -->
      <template #actions="{ item }">
        <div class="actions">
          <button @click="$emit('editExpense', item)" class="edit">Edit</button>
          <button @click="remove(item.id)" class="delete">Delete</button>
        </div>
      </template>
      
      <!-- Empty State -->
      <template #empty>
        No expenses found
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useExpenseStore } from '@/stores/Expense';
import { useGroupStore } from '@/stores/Group';

import DataTable from '@/components/DataTable.vue';

const emit = defineEmits(['editExpense']);
const expenseStore = useExpenseStore();
const groupStore = useGroupStore();

const expenses = computed(() => expenseStore.expenses);

const columns = [
  { key: 'expense_name', label: 'Name' },
  { 
    key: 'expense_date', 
    label: 'Date',
    format: (item) => formatDate(item.expense_date)
  },
  { key: 'amount', label: 'Amount' },
  { key: 'group_id', label: 'Group' },
  { key: 'actions', label: 'Actions' }
];

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-IN');
}

function getGroupName(groupId) {
  const group = groupStore.groups.find(g => g.id === groupId);
  return group ? group.group_name : 'Uncategorized';
}

async function remove(id) {
  if (confirm('Delete this expense?')) {
    await expenseStore.deleteExpense(id);
  }
}

onMounted(() => {
  expenseStore.fetchExpenses();
  groupStore.fetchGroups();
});
</script>

