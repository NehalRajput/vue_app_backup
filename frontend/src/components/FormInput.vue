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
  
  <style scoped>
.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}

.form-label {
  color: #333; /* Dark label */
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
  
}

.form-input {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #ddd;
  border-radius: 0.5rem;
  background: #fff;
  color: #333;
  font-size: 1rem;
  outline: none;
  transition: border-color 0.3s, background 0.3s;
  box-sizing: border-box;
}


.form-input:focus {
  border-color: #007bff;        
  background: #f0f8ff;         
}
</style>
