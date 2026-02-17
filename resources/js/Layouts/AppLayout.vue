<template>
  <div class="min-h-screen bg-gray-50 dark:bg-slate-950">
    <!-- Sidebar -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-40 bg-black/50 lg:hidden"
      @click="sidebarOpen = false"
    ></div>

    <aside
      :class="[
        'fixed left-0 top-0 h-full z-50 w-64 bg-white dark:bg-slate-900 border-r border-gray-200 dark:border-slate-700',
        'transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-auto',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- Logo -->
      <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200 dark:border-slate-700">
        <Link :href="route('welcome')" class="flex items-center gap-2 group">
          <div class="w-8 h-8 bg-gradient-to-br from-emerald-600 to-green-600 rounded-lg flex items-center justify-center shadow-lg">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <span class="text-lg font-bold text-gray-900 dark:text-white">ErrandRunner</span>
        </Link>
        <button
          @click="sidebarOpen = false"
          class="lg:hidden p-1 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800"
        >
          <i class="fas fa-times text-lg"></i>
        </button>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-2">
        <!-- Dashboard Link -->
        <NavLink
          :href="isAdmin ? route('admin.dashboard') : route('user.dashboard')"
          :active="route().current('admin.dashboard') || route().current('user.dashboard')"
          icon="fa-chart-line"
        >
          Dashboard
        </NavLink>

        <template v-if="isAdmin">
          <!-- Admin Navigation -->
          <div class="pt-4">
            <p class="px-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Admin</p>
            <NavLink
              href="#"
              icon="fa-users"
              class="mt-2"
            >
              Users Management
            </NavLink>
            <NavLink
              href="#"
              icon="fa-sliders-h"
            >
              Settings
            </NavLink>
            <NavLink
              href="#"
              icon="fa-chart-bar"
            >
              Reports
            </NavLink>
          </div>
        </template>

        <template v-else>
          <!-- User Navigation -->
          <div class="pt-4">
            <p class="px-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">My Space</p>
            <NavLink
              href="#"
              icon="fa-tasks"
              class="mt-2"
            >
              My Tasks
            </NavLink>
            <NavLink
              href="#"
              icon="fa-shopping-bags"
            >
              My Errands
            </NavLink>
            <NavLink
              href="#"
              icon="fa-calendar"
            >
              Schedule
            </NavLink>
          </div>

          <!-- User Account Section -->
          <div class="pt-4">
            <p class="px-3 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Account</p>
            <NavLink
              :href="route('user.profile.edit')"
              :active="route().current('user.profile.edit')"
              icon="fa-user-circle"
              class="mt-2"
            >
              Profile Settings
            </NavLink>
          </div>
        </template>
      </nav>

      <!-- User Menu at Bottom -->
      <div class="border-t border-gray-200 dark:border-slate-700 p-4">
        <button
          @click="userMenuOpen = !userMenuOpen"
          class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors"
        >
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center text-white text-sm font-semibold">
              {{ user.name.charAt(0).toUpperCase() }}
            </div>
            <div class="text-left">
              <p class="text-sm font-medium text-gray-900 dark:text-white">{{ user.name }}</p>
              <p class="text-xs text-gray-600 dark:text-gray-400 capitalize">{{ user.role }}</p>
            </div>
          </div>
          <i :class="`fas fa-chevron-down transition-transform ${userMenuOpen ? 'rotate-180' : ''}`"></i>
        </button>

        <!-- User Dropdown Menu -->
        <div v-if="userMenuOpen" class="mt-2 space-y-1 border-t border-gray-200 dark:border-slate-700 pt-2">
          <Link
            :href="route('user.profile.edit')"
            @click="userMenuOpen = false"
            class="block w-full text-left px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
          >
            <i class="fas fa-user-circle mr-2"></i>
            Profile
          </Link>
          <form @submit.prevent="logout" class="w-full">
            <button
              type="submit"
              class="w-full text-left px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors flex items-center gap-2"
            >
              <i class="fas fa-sign-out-alt"></i>
              Sign Out
            </button>
          </form>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-64">
      <!-- Top Navigation -->
      <header class="h-16 bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between px-6 sticky top-0 z-40">
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="lg:hidden p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800"
        >
          <i class="fas fa-bars text-xl"></i>
        </button>

        <div class="flex-1"></div>

        <!-- Top Right Actions -->
        <div class="flex items-center gap-4">
          <!-- Notifications -->
          <button class="p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition-colors relative">
            <i class="fas fa-bell text-lg"></i>
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
          </button>

          <!-- Theme Toggle -->
          <button
            @click="toggleTheme"
            class="p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
          >
            <i :class="`fas ${isDark ? 'fa-sun' : 'fa-moon'}`"></i>
          </button>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

const sidebarOpen = ref(false);
const userMenuOpen = ref(false);
const isDark = ref(false);
const page = usePage();

const user = computed(() => page.props.auth.user);
const isAdmin = computed(() => user.value?.role === 'admin');

const toggleTheme = () => {
  isDark.value = !isDark.value;
  // In a real app, you'd save this preference and apply it to the document
};

const logout = () => {
  router.post(route('logout'));
};
</script>

<style scoped>
/* Layout styles */
</style>
