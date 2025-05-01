<template>
  <form @submit.prevent="handleSubmit" class="form-container">
    <h2 class="form-title">{{ formTitle }}</h2>
    
    <FormInput
      label="Group Name"
      v-model="form.group_name"
      placeholder="e.g., Vacation, Home, etc."
      required
    />
    
    <button type="submit" class="submit-button">
      {{ formButtonText }}
    </button>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useGroupStore } from '@/stores/Group';
import FormInput from '@/components/FormInput.vue';

const groupStore = useGroupStore();

const form = ref({
  group_name: '',
});

const formTitle = ref('Create New Group');
const formButtonText = ref('Create Group');
const editingGroupId = ref(null);

const props = defineProps({
  group: {
    type: Object,
    required: false
  }
});

// Watch for edit mode
watch(() => props.group, (newGroup) => {
  if (newGroup) {
    form.value = {
      group_name: newGroup.group_name,
    };
    editingGroupId.value = newGroup.id;
    formTitle.value = 'Edit Group';
    formButtonText.value = 'Save Changes';
  }
});

const handleSubmit = async () => {
  if (editingGroupId.value) {
    await groupStore.updateGroup(editingGroupId.value, form.value);
  } else {
    await groupStore.addGroup(form.value);
  }
  
  // Reset form
  form.value = {
    group_name: ''
  };
  editingGroupId.value = null;
  formTitle.value = 'Create New Group';
  formButtonText.value = 'Create Group';
};
</script>

<style scoped>
.form-container {
  max-width: 500px;
  margin: 0 auto;
  padding: 1.5rem;
  background-color: #000;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.form-title {
  color: #fff;
  text-align: center;
  margin-bottom: 1.5rem;
}

.submit-button {
  width: 100%;
  padding: 0.75rem;
  margin-top: 1.5rem;
  background-color: #fff;
  color: #000;
  border: none;
  border-radius: 0.25rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.submit-button:hover {
  background-color: #ddd;
}
</style>