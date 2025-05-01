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

<style scoped>
.expense-list {
  width: 100%;
}

.amount {
  font-weight: 500;
}

.group-tag {
  background-color: #f1f5f9;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.85rem;
}

.actions {
  display: flex;
  gap: 8px;
}

button {
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 0.875rem;
  cursor: pointer;
  border: none;
}

.edit {
  background-color: #e0f2fe;
  color: #0369a1;
}

.delete {
  background-color: #fee2e2;
  color: #b91c1c;
}
</style>