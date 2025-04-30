<template>
  <div class="expense-list">  <!-- reuse expense-list wrapper -->
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="group in groupStore.groups"
          :key="group.id"
        >
          <td>{{ group.group_name }}</td>
          <td class="text-right actions">
            <button
              @click="$emit('editGroup', group)"
              class="edit"
            >
              Edit
            </button>
            <button
              @click="groupStore.deleteGroup(group.id)"
              class="delete"
            >
              Delete
            </button>
          </td>
        </tr>

        <tr v-if="groupStore.groups.length === 0">
          <td colspan="2" class="empty">
            No groups created yet
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useGroupStore } from '@/stores/group';

const groupStore = useGroupStore();
onMounted(() => {
  console.log('Groups in store:', groupStore.groups); // Add this
});


</script>

<style scoped>
/* Bring in your existing expense-list styles */
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
  color: black;
}

th {
  background-color: #f8fafc;
  font-weight: 600;
  color: black;
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
  justify-content: flex-end;
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
