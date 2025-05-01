<template>
  <div class="expense-list">
    <DataTable :items="groupStore.groups" :columns="columns">
      <!-- Actions Column -->
      <template #actions="{ item }">
        <div class="actions">
          <button @click="$emit('editGroup', item)" class="edit">
            Edit
          </button>
          <button @click="groupStore.deleteGroup(item.id)" class="delete">
            Delete
          </button>
        </div>
      </template>
      
      <!-- Empty State -->
      <template #empty>
        No groups created yet
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import { onMounted, defineEmits } from 'vue';
import { useGroupStore } from '@/stores/Group';
import DataTable from '@/components/DataTable.vue';

defineEmits(['editGroup']);
const groupStore = useGroupStore();

const columns = [
  { key: 'group_name', label: 'Name' },
  { key: 'actions', label: 'Actions' }
];

onMounted(() => {
  groupStore.fetchGroups();
  console.log('Groups in store:', groupStore.groups);
});
</script>

<style scoped>
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
</style>