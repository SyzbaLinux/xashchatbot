<template>
  <div class="mb-6">
    <label :for="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
      <span v-if="icon" class="inline-block mr-2">
        <i :class="`fas ${icon} text-emerald-600 dark:text-emerald-400`"></i>
      </span>
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <div class="relative">
      <input
        :id="id"
        :type="computedType"
        :value="modelValue"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :autocomplete="autocomplete"
        @input="$emit('update:modelValue', $event.target.value)"
        :class="[
          'w-full px-4 py-2.5 border rounded-lg transition-all duration-200',
          'bg-white dark:bg-slate-800',
          'text-gray-900 dark:text-gray-100',
          'placeholder-gray-400 dark:placeholder-gray-500',
          'font-medium',
          isPasswordType && 'pr-12',
          error
            ? 'border-red-500 dark:border-red-400 focus:ring-2 focus:ring-red-500 focus:border-transparent'
            : 'border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:focus:ring-emerald-400',
          disabled && 'opacity-50 cursor-not-allowed'
        ]"
      />

      <!-- Password visibility toggle -->
      <button
        v-if="isPasswordType"
        type="button"
        @click="showPassword = !showPassword"
        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors p-1"
        :title="showPassword ? 'Hide password' : 'Show password'"
      >
        <i :class="`fas ${showPassword ? 'fa-eye-slash' : 'fa-eye'}`"></i>
      </button>
    </div>

    <p v-if="error" class="text-sm text-red-600 dark:text-red-400 mt-2 flex items-center">
      <i class="fas fa-exclamation-circle mr-1.5"></i>
      {{ error }}
    </p>

    <p v-if="hint && !error" class="text-sm text-gray-500 dark:text-gray-400 mt-2">
      {{ hint }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';

const props = withDefaults(
  defineProps<{
    modelValue: string;
    type?: 'text' | 'email' | 'password' | 'number' | 'tel' | 'url';
    label: string;
    id?: string;
    placeholder?: string;
    error?: string;
    required?: boolean;
    icon?: string;
    hint?: string;
    disabled?: boolean;
    autocomplete?: string;
  }>(),
  {
    type: 'text',
    placeholder: '',
    required: false,
    disabled: false,
    autocomplete: 'off',
  }
);

defineEmits<{
  'update:modelValue': [value: string];
}>();

const showPassword = ref(false);

// Generate unique ID if not provided
const id = computed(() => props.id || `input-${Math.random().toString(36).substr(2, 9)}`);

// Check if this is a password type
const isPasswordType = computed(() => props.type === 'password');

// Compute the actual input type based on showPassword state
const computedType = computed(() => {
  if (!isPasswordType.value) return props.type;
  return showPassword.value ? 'text' : 'password';
});
</script>

<style scoped>
/* Input animations */
input:focus {
  outline: none;
}
</style>
