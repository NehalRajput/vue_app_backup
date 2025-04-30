<template>
  <div class="form-wrapper">
    <h2 class="form-title">{{ isEdit ? 'Update' : 'Add' }} Expense</h2>
    <div class="form-content">
      <!-- Expense Name -->
      <div class="form-group">
        <label class="form-label">Expense Name</label>
        <input
          v-model="form.expense_name"
          type="text"
          placeholder="e.g. Groceries"
          class="form-input"
          required
        />
      </div>

      <!-- Amount -->
      <div class="form-group">
        <label class="form-label">Amount (₹)</label>
        <input
          v-model.number="form.amount"
          type="number"
          placeholder="e.g. 1500"
          class="form-input"
          required
        />
      </div>

      <!-- Group -->
      <div class="form-group">
        <label class="form-label">Group</label>
        <select v-model="form.group_id" class="form-input" required>
          <option disabled value="">Select a group</option>
          <option v-for="group in groups" :key="group.id" :value="group.id">
         {{ group.group_name }} 


          </option>
        </select>
      </div>

      <!-- Date -->
      <div class="form-group">
        <label class="form-label">Date</label>
        <input
          v-model="form.expense_date"
          type="date"
          class="form-input"
          required
        />
      </div>

      <!-- Submit Button -->
      <button type="submit" @click="handleSubmit" class="form-button">
        {{ isEdit ? 'Update' : 'Add' }} Expense
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, watch } from 'vue';
import { useExpenseStore } from '@/stores/expense';
import { useGroupStore } from '@/stores/group';

const props = defineProps({ expense: Object, groupId: Number });
const emit = defineEmits(['submitted', 'group-changed']);

const expenseStore = useExpenseStore();
const groupStore = useGroupStore();
groupStore.fetchGroups(); // Fetch groups from the store

const groups = computed(() => groupStore.groups);

const form = reactive({
  expense_name: '',
  amount: '',
  expense_date: '',
  group_id: props.groupId || ''
});

const isEdit = computed(() => !!props.expense?.id);

watch(
  () => props.expense,
  (n) => {
    if (n) {
      form.expense_name = n.expense_name;
      form.amount = n.amount;
      form.expense_date = n.expense_date;
      form.group_id = n.group_id;
      emit('group-changed', n.group_id); // Emit group change on expense edit
    }
  },
  { immediate: true }
);

async function handleSubmit() {
  const expense = {
    expense_name: form.expense_name,
    amount: Number(form.amount),
    expense_date: form.expense_date,
    group_id: Number(form.group_id),

  
  };

  console.log(expense); // Log the form data before submitting

  try {
    if (isEdit.value) {
      await expenseStore.updateExpense(props.expense.id, expense);
    } else {
      await expenseStore.addExpense(expense);
    }
    emit('submitted');
  } catch (error) {
    console.error("Error submitting expense:", error);
  }

  // Reset form after submission
  form.expense_name = '';
  form.amount = '';
  form.expense_date = '';
  form.group_name = props.groupId || '';
}
</script>

<style scoped>
.form-wrapper {
  max-width: 500px;
  margin: 2rem auto;
  background: #000;
  padding: 2rem;
  border-radius: 1rem;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
}
.form-title {
  color: #fff;
  text-align: center;
  font-size: 1.75rem;
  margin-bottom: 1.5rem;
}
.form-content {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}
.form-group {
  display: flex;
  flex-direction: column;
}
.form-label {
  color: #ddd;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}
.form-input {
  padding: 0.75rem 1rem;
  border: 1px solid #444;
  border-radius: 0.5rem;
  background: #111;
  color: #eee;
  font-size: 1rem;
  outline: none;
  transition: border-color 0.3s, background 0.3s;
}
.form-input:focus {
  border-color: #fff;
  background: #222;
}
.form-button {
  padding: 0.75rem;
  background: #fff;
  color: #000;
  font-weight: bold;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: background 0.3s;
}
.form-button:hover {
  background: #ddd;
}
</style>
