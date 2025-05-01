<template>
    <div class="field">
      <label :for="id">{{ label }}</label>
      <input
        :id="id"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :type="type"
        :placeholder="placeholder"
        :required="required"
        class="input"
      />
    </div>
  </template>
  
  <script setup>
  defineProps({
    id: {
      type: String,
      required: true
    },
    label: {
      type: String,
      required: true
    },
    modelValue: {
      type: [String, Number],
      default: ''
    },
    type: {
      type: String,
      default: 'text'
    },
    placeholder: {
      type: String,
      default: ''
    },
    required: {
      type: Boolean,
      default: false
    }
  });
  
  defineEmits(['update:modelValue']);
  </script>
  
  <style scoped>
  .field {
    margin-bottom: 12px;
    width: 100%;
  }
  
  label {
    display: block;
    margin-bottom: 4px;
    color: #000;
    font-weight: 500;
  }
  
  .input {
    width: 100%;
    padding: 12px;
    border: 1px solid #000;
    border-radius: 4px;
    font-size: 1rem;
    outline: none;
    color: #000;
  }
  
  .input:focus {
    border-color: #000;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
  }
  </style>