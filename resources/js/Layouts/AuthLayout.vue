<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50 to-green-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 flex items-center justify-center px-4 py-12">
    <!-- Animated background elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-200 dark:bg-emerald-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-green-200 dark:bg-green-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 w-full">
      <!-- Logo/Brand section -->
      <div class="text-center mb-8">
        <div class="flex justify-center mb-4">
          <div class="w-12 h-12 bg-gradient-to-br from-emerald-600 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">ErrandRunner</h1>
      </div>

      <!-- Slot for page content -->
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import { useFlashMessages } from '@/Composables/useFlashMessages';

// Initialize flash message watcher
useFlashMessages();
</script>

<style scoped>
/* Additional animations if needed */
</style>
