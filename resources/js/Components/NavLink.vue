<template>
  <!-- External URL → plain anchor tag -->
  <a
    v-if="isExternal"
    :href="href"
    target="_blank"
    rel="noopener noreferrer"
    :class="linkClass"
  >
    <i :class="`fas ${icon} w-5`"></i>
    <slot />
  </a>

  <!-- Internal route → Inertia Link -->
  <Link
    v-else
    :href="href"
    :class="linkClass"
  >
    <i :class="`fas ${icon} w-5`"></i>
    <slot />
  </Link>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
  href: string;
  active?: boolean;
  icon: string;
}>();

const isExternal = computed(() => /^https?:\/\//.test(props.href));

const linkClass = computed(() => [
  'flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-all duration-200',
  props.active
    ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800',
]);
</script>
