<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50 to-green-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 flex flex-col">
    <!-- Animated background elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-emerald-200 dark:bg-emerald-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-green-200 dark:bg-green-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <!-- Navigation -->
    <nav class="relative z-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-gray-200/50 dark:border-slate-700/50 sticky top-0">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo/Brand -->
          <Link :href="route('welcome')" class="flex items-center gap-2 group">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-green-600 rounded-lg flex items-center justify-center shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-300">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
            </div>
            <span class="hidden sm:block text-xl font-bold bg-gradient-to-r from-emerald-600 to-green-600 dark:from-emerald-400 dark:to-green-400 bg-clip-text text-transparent">ErrandRunner</span>
          </Link>

          <!-- Desktop Navigation -->
          <div class="hidden md:flex items-center gap-8">
            <Link
              :href="route('welcome')"
              class="text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium transition-colors"
            >
              Home
            </Link>

            <div v-if="!user" class="flex items-center gap-4">
              <Link
                :href="route('login')"
                class="text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium transition-colors"
              >
                Sign In
              </Link>
              <Link
                :href="route('register')"
                class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-green-600 dark:from-emerald-500 dark:to-green-500 text-white font-semibold rounded-lg hover:shadow-lg hover:scale-105 transition-all duration-300"
              >
                Sign Up
              </Link>
            </div>

            <div v-else class="flex items-center gap-4">
              <Link
                :href="getDashboardRoute(user.role)"
                class="text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-medium transition-colors"
              >
                Dashboard
              </Link>
              <form @submit.prevent="logout">
                <button
                  type="submit"
                  class="text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors"
                >
                  Sign Out
                </button>
              </form>
            </div>
          </div>

          <!-- Mobile menu button -->
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="md:hidden p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors"
          >
            <svg
              class="w-6 h-6"
              :class="{ 'rotate-90': mobileMenuOpen }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <div
          v-if="mobileMenuOpen"
          class="md:hidden border-t border-gray-200/50 dark:border-slate-700/50 py-3 space-y-2"
        >
          <Link
            :href="route('welcome')"
            class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-800/50 rounded-lg transition-colors"
          >
            Home
          </Link>

          <template v-if="!user">
            <Link
              :href="route('login')"
              class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-800/50 rounded-lg transition-colors"
            >
              Sign In
            </Link>
            <Link
              :href="route('register')"
              class="block px-4 py-2 bg-gradient-to-r from-emerald-600 to-green-600 dark:from-emerald-500 dark:to-green-500 text-white font-semibold rounded-lg text-center transition-all"
            >
              Sign Up
            </Link>
          </template>

          <template v-else>
            <Link
              :href="getDashboardRoute(user.role)"
              class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-slate-800/50 rounded-lg transition-colors"
            >
              Dashboard
            </Link>
            <form @submit.prevent="logout" class="px-2">
              <button
                type="submit"
                class="w-full text-left px-2 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors font-medium"
              >
                Sign Out
              </button>
            </form>
          </template>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <div class="relative z-10 flex-1 flex items-center justify-center px-4 py-4">
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
