<template>
  <div class="form-wrapper">
    <h2 class="form-title">{{ isEdit ? 'Update' : 'Add' }} Expense</h2>
    <div class="form-content">
      <!-- Expense Name -->
      <FormInput
        label="Expense Name"
        v-model="form.expense_name"
        placeholder="e.g. Groceries"
        required
      />

      <!-- Amount -->
      <FormInput
        label="Amount (₹)"
        v-model="form.amount"
        type="number"
        placeholder="e.g. 1500"
        required
      />

      <!-- Group -->
      <FormInput
        label="Group"
        v-model="form.group_id"
        type="select"
        placeholder="Select a group"
        required
      >
        <option v-for="group in groups" :key="group.id" :value="group.id">
          {{ group.group_name }}
        </option>
      </FormInput>

      <!-- Date -->
      <FormInput
        label="Date"
        v-model="form.expense_date"
        type="date"
        required
      />

      <!-- Submit Button -->
      <button type="submit" @click="handleSubmit" class="form-button">
        {{ isEdit ? 'Update' : 'Add' }} Expense
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, watch } from 'vue';
import { useExpenseStore } from '@/stores/Expense';
import { useGroupStore } from '@/stores/Group';
import FormInput from '@/components/FormInput.vue';

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
  form.group_id = props.groupId || '';
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