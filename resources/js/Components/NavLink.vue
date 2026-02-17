<template>
  <Link
    :href="href"
    :class="[
      'flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-all duration-200',
      active
        ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300'
        : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800'
    ]"
  >
    <i :class="`fas ${icon} w-5`"></i>
    <slot />
  </Link>
</template>

<script setup lang="ts">
defineProps<{
  href: string;
  active?: boolean;
  icon: string;
}>();
</script>

<style scoped>
/* Component styles */
</style>
