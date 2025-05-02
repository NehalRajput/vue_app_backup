<template>
  <div class="group-page">
    <div class="header">
      <h1>Groups</h1>
      <button 
        @click="toggleForm" 
        class="add-button"
      >
        {{ showForm ? 'Cancel' : 'Add Group' }}
      </button>
    </div>

    <!-- Form (shown only when showForm is true or when editing) -->
    <GroupForm
      v-if="showForm || selectedGroup"
      :group="selectedGroup"
      @submitted="handleSaveComplete"
      @cancel="handleCancel"
    />

    <!-- Group List -->
    <GroupList 
      @editGroup="handleEditGroup" 
      class="list-section"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useGroupStore } from '@/stores/Group.js';
import GroupForm from '@/components/Group/GroupForm.vue';
import GroupList from '@/components/Group/GroupList.vue';

const groupStore = useGroupStore();
const showForm = ref(false);
const selectedGroup = ref(null);

const toggleForm = () => {
  showForm.value = !showForm.value;
  if (!showForm.value) {
    selectedGroup.value = null;
  }
};

const handleEditGroup = (group) => {
  selectedGroup.value = group;
  showForm.value = true;
};

const handleSaveComplete = () => {
  showForm.value = false;
  selectedGroup.value = null;
  groupStore.fetchGroups(); // Refresh the list after saving
};

const handleCancel = () => {
  showForm.value = false;
  selectedGroup.value = null;
};

onMounted(() => {
  groupStore.fetchGroups();
});
</script>

<style scoped>
.group-page {
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