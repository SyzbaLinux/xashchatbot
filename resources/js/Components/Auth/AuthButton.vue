<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'w-full px-4 py-2.5 rounded-lg font-semibold text-white transition-all duration-200',
      'flex items-center justify-center gap-2',
      'focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-slate-900',

      // Variant styles
      variant === 'primary'
        ? 'bg-gradient-to-r from-emerald-600 to-green-600 dark:from-emerald-500 dark:to-green-500 hover:shadow-lg hover:scale-105 focus:ring-emerald-500 dark:focus:ring-emerald-400'
        : variant === 'secondary'
        ? 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 hover:bg-gray-300 dark:hover:bg-gray-600 focus:ring-gray-400'
        : 'bg-red-600 dark:bg-red-500 hover:shadow-lg hover:scale-105 focus:ring-red-500 dark:focus:ring-red-400',

      // States
      (disabled || loading) && 'opacity-60 cursor-not-allowed',
      !disabled && !loading && 'hover:shadow-lg',
    ]"
  >
    <i v-if="loading" class="fas fa-spinner animate-spin"></i>
    <i v-else-if="icon" :class="`fas ${icon}`"></i>
    <span>{{ loading ? loadingText : label }}</span>
  </button>
</template>

<script setup lang="ts">
withDefaults(
  defineProps<{
    label: string;
    type?: 'button' | 'submit' | 'reset';
    variant?: 'primary' | 'secondary' | 'danger';
    disabled?: boolean;
    loading?: boolean;
    loadingText?: string;
    icon?: string;
  }>(),
  {
    type: 'button',
    variant: 'primary',
    disabled: false,
    loading: false,
    loadingText: 'Loading...',
  }
);
</script>

<style scoped>
button:active:not(:disabled) {
  transform: scale(0.98);
}
</style>
