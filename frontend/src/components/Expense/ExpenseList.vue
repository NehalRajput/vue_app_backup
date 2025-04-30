<template>
  <div class="expense-list">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Date</th>
          <th>Amount</th>
          <th>Group</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="expense in expenses" :key="expense.id">
          <td>{{ expense.expense_name }}</td>
          <td>{{ formatDate(expense.expense_date) }}</td>
          <td class="amount">₹{{ expense.amount }}</td>
          <td>
            <span class="group-tag">
              {{ getGroupName(expense.group_id) }}
            </span>
          </td>
          <td class="actions">
            <button @click="$emit('editExpense', expense)" class="edit">Edit</button>
            <button @click="remove(expense.id)" class="delete">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="!expenses.length" class="empty">
      No expenses found
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useExpenseStore } from '@/stores/expense';
import { useGroupStore } from '@/stores/group';

const emit = defineEmits(['editExpense']);
const expenseStore = useExpenseStore();
const groupStore = useGroupStore();

const expenses = computed(() => expenseStore.expenses);

function formatDate(date) {
  return new Date(date).toLocaleDateString('en-IN');
}



function getGroupName(groupId) {
  const group = groupStore.groups.find(g => g.id === groupId);
  return group ? group.group_name : 'Uncategorized'; // FIXED key
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
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 12px 16px;
  text-align: left;
  border-bottom: 1px solid #e2e8f0;
  color:black;
}

th {
  background-color: #f8fafc;
  font-weight: 600;
  color:black;
}

tr:hover {
  background-color: #f8fafc;
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

.empty {
  padding: 24px;
  text-align: center;
  color: #64748b;
}
</style>