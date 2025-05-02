<template>
    <div class="data-table">
      <table v-if="items.length">
        <thead>
          <tr>
            <th v-for="column in columns" :key="column.key">{{ column.label }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in items" :key="index">
            <td v-for="column in columns" :key="`${index}-${column.key}`">
              <slot :name="column.key" :item="item" :value="getValue(item, column)">
                {{ getValue(item, column) }}
              </slot>
            </td>
          </tr>
        </tbody>
      </table>
      
      <div v-else class="empty">
        <slot name="empty">No data available</slot>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps } from 'vue';
  import '@/assets/css/Datatable.css';
  
  const props = defineProps({
    items: {
      type: Array,
      required: true
    },
    columns: {
      type: Array,
      required: true
    }
  });
  
  function getValue(item, column) {
    if (column.format) {
      return column.format(item);
    }
    
    // Handle nested properties (e.g. 'user.name')
    if (column.key.includes('.')) {
      return column.key.split('.').reduce((o, i) => (o ? o[i] : ''), item);
    }
    
    return item[column.key];
  }
  </script>
  
 