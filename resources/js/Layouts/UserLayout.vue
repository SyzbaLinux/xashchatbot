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
        'transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- Logo -->
      <div class="h-16 flex items-center justify-between px-6 border-b border-gray-200 dark:border-slate-700 flex-shrink-0">
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
      <nav class="overflow-y-auto px-3 py-4 space-y-1 flex-1">
        <!-- Dashboard Link -->
        <NavLink
          :href="route('user.dashboard')"
          :active="route().current('user.dashboard')"
          icon="fa-chart-line"
          class="text-sm"
        >
          Dashboard
        </NavLink>

        <!-- My Space Section -->
        <div class="pt-2">
          <p class="px-2 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">My Space</p>
          <NavLink
            href="#"
            icon="fa-tasks"
            class="mt-1 text-sm"
          >
            My Tasks
          </NavLink>
          <NavLink
            href="#"
            icon="fa-shopping-bags"
            class="text-sm"
          >
            My Errands
          </NavLink>
          <NavLink
            href="#"
            icon="fa-calendar"
            class="text-sm"
          >
            Schedule
          </NavLink>
          <NavLink
            href="#"
            icon="fa-history"
            class="text-sm"
          >
            History
          </NavLink>
        </div>

        <!-- My Earnings Section -->
        <div class="pt-2">
          <p class="px-2 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Earnings</p>
          <NavLink
            href="#"
            icon="fa-wallet"
            class="mt-1 text-sm"
          >
            Wallet
          </NavLink>
          <NavLink
            href="#"
            icon="fa-chart-pie"
            class="text-sm"
          >
            Earnings
          </NavLink>
          <NavLink
            href="#"
            icon="fa-bank"
            class="text-sm"
          >
            Payouts
          </NavLink>
        </div>

        <!-- My Account Section -->
        <div class="pt-2">
          <p class="px-2 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">Account</p>
          <NavLink
            :href="route('user.profile.edit')"
            :active="route().current('user.profile.edit')"
            icon="fa-user-circle"
            class="mt-1 text-sm"
          >
            Profile Settings
          </NavLink>
          <NavLink
            href="#"
            icon="fa-bell"
            class="text-sm"
          >
            Notifications
          </NavLink>
        </div>
      </nav>

      <!-- User Footer -->
      <div class="border-t border-gray-200 dark:border-slate-700 p-3 bg-white dark:bg-slate-900 relative flex-shrink-0">
        <button
          @click="userMenuOpen = !userMenuOpen"
          class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors"
        >
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center text-white text-xs font-semibold">
              {{ user.name.charAt(0).toUpperCase() }}
            </div>
            <div class="text-left min-w-0">
              <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ user.name }}</p>
              <p class="text-xs text-gray-600 dark:text-gray-400 capitalize truncate">{{ user.role }}</p>
            </div>
          </div>
          <i :class="`fas fa-chevron-down transition-transform text-xs ${userMenuOpen ? 'rotate-180' : ''}`"></i>
        </button>

        <!-- User Dropdown Menu (Drop Up) -->
        <div v-if="userMenuOpen" class="absolute bottom-full mb-2 left-0 right-0 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 z-10 mx-1">
          <div class="space-y-1 p-1">
            <Link
              :href="route('user.profile.edit')"
              @click="userMenuOpen = false"
              class="block w-full text-left px-2 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-slate-800 rounded transition-colors"
            >
              <i class="fas fa-user-circle mr-1.5"></i>
              Profile
            </Link>
            <form @submit.prevent="logout" class="w-full">
              <button
                type="submit"
                class="w-full text-left px-2 py-1.5 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors flex items-center gap-1.5"
              >
                <i class="fas fa-sign-out-alt"></i>
                Sign Out
              </button>
            </form>
          </div>
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
          <!-- User Badge -->
          <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 text-xs font-semibold rounded-full flex items-center gap-1">
            <i class="fas fa-user"></i>
            User
          </span>

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
