<template>
  <form @submit.prevent="handleSubmit" class="form-container">
    <h2 class="form-title">{{ formTitle }}</h2>

    <div class="form-group">
      <label for="group_name">Group Name</label>
      <input
        id="group_name"
        v-model="form.group_name"
        type="text"
        required
        class="form-input"
        placeholder="e.g., Vacation, Home, etc."
      />
    </div>



    <button type="submit" class="submit-button">
      {{ formButtonText }}
    </button>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useGroupStore } from '@/stores/group';

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
