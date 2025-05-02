<template>
    
  <div class="form-group">

      <label v-if="label" class="form-label">{{ label }}</label>
      <input
        v-if="type !== 'select'"
        :type="type"
        :value="modelValue"
        @input="updateValue($event)"
        :placeholder="placeholder"
        class="form-input"
        :required="required"
      />
      <select
        v-else
        :value="modelValue"
        @change="updateValue($event)"
        class="form-input"
        :required="required"
      >
        <option v-if="placeholder" disabled value="">{{ placeholder }}</option>
        <slot></slot>
      </select>
    </div>
  </template>
  
  <script setup>
  import '@/assets/css/form.css';
  defineProps({
    label: String,
    modelValue: [String, Number],
    type: {
      type: String,
      default: 'text'
    },
    placeholder: String,
    required: {
      type: Boolean,
      default: false
    }
  });
  
  const emit = defineEmits(['update:modelValue']);
  
  function updateValue(event) {
    let value = event.target.value;
    
    // Convert value to number for number inputs
    if (event.target.type === 'number') {
      value = value === '' ? '' : Number(value);
    }
    
    emit('update:modelValue', value);
  }
  </script>
  
