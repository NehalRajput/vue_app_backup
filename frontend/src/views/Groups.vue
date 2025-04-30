<template>

    <h1>Groups</h1>
    <div class="group-page">
      <GroupForm :group="selectedGroup" />
      <GroupList @editGroup="handleEditGroup" />
    </div>
 
</template>

<script setup>
import { ref,onMounted } from 'vue';
import {useGroupStore} from '@/stores/group.js';

import GroupForm from '@/components/Group/GroupForm.vue';
import GroupList from '@/components/Group/GroupList.vue';

const groupStore=useGroupStore()
const selectedGroup = ref(null);

const handleEditGroup = (group) => {
  selectedGroup.value = group;
};

onMounted(() => {
  groupStore.fetchGroups();
});
</script>
