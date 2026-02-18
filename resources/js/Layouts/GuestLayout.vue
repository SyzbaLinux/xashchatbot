<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50 to-green-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 flex flex-col">
    <!-- Animated background elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-200 dark:bg-emerald-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-green-200 dark:bg-green-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
    </div>

     <!-- Content -->
    <div class="relative z-10 flex-1 flex items-center justify-center px-4">
      <div class="w-full">
        <!-- Slot for page content -->
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { useFlashMessages } from '@/Composables/useFlashMessages';

// Initialize flash message watcher
useFlashMessages();

const mobileMenuOpen = ref(false);
const page = usePage();

// Get the authenticated user from page props
const user = computed(() => page.props.auth.user);

// Get dashboard route based on user role
const getDashboardRoute = (role: string) => {
  return role === 'admin' ? route('admin.dashboard') : route('user.dashboard');
};

const logout = () => {
  router.post(route('logout'));
};
</script>

<style scoped>
/* Layout styles */
</style>
