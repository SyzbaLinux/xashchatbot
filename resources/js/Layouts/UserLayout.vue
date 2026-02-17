<template>
  <div class="min-h-screen bg-gray-50 dark:bg-slate-950">
    <!-- Mobile overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-40 bg-black/50 lg:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed left-0 top-0 h-full z-50 w-64 bg-white dark:bg-slate-900 border-r border-gray-200 dark:border-slate-700',
        'transform transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- Logo -->
      <div class="h-16 flex items-center justify-between px-5 border-b border-gray-200 dark:border-slate-700 flex-shrink-0">
        <Link :href="route('welcome')" class="flex items-center gap-2.5 group">
          <div class="w-8 h-8 xash-gradient rounded-lg flex items-center justify-center shadow-md">
            <i class="fas fa-comments-dollar text-white text-sm"></i>
          </div>
          <span class="text-lg font-bold text-gray-900 dark:text-white tracking-tight">XASH</span>
        </Link>
        <button
          @click="sidebarOpen = false"
          class="lg:hidden p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Navigation -->
      <nav class="overflow-y-auto px-3 py-4 space-y-0.5 flex-1">

        <!-- Dashboard -->
        <NavLink
          :href="route('user.dashboard')"
          :active="route().current('user.dashboard')"
          icon="fa-chart-line"
          class="text-sm"
        >
          Dashboard
        </NavLink>

        <!-- Services -->
        <div class="pt-3">
          <p class="px-2 pb-1 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            Services
          </p>
          <NavLink href="https://wa.me/263774916256" icon="fa-mobile-alt" class="text-sm">
            Buy Airtime
          </NavLink>
          <NavLink href="https://wa.me/263774916256" icon="fa-wifi" class="text-sm">
            Buy Data Bundles
          </NavLink>
          <NavLink href="https://wa.me/263774916256" icon="fa-file-invoice-dollar" class="text-sm">
            Pay Utility Bills
          </NavLink>
        </div>

        <!-- History -->
        <div class="pt-3">
          <p class="px-2 pb-1 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            History
          </p>
          <NavLink href="#" icon="fa-receipt" class="text-sm">
            Transactions
          </NavLink>
        </div>

        <!-- Account -->
        <div class="pt-3">
          <p class="px-2 pb-1 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
            Account
          </p>
          <NavLink
            :href="route('user.profile.edit')"
            :active="route().current('user.profile.edit')"
            icon="fa-user-circle"
            class="text-sm"
          >
            Profile Settings
          </NavLink>
          <NavLink href="#" icon="fa-bell" class="text-sm">
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
            <div class="w-7 h-7 rounded-full xash-gradient flex items-center justify-center text-white text-xs font-bold">
              {{ user.name.charAt(0).toUpperCase() }}
            </div>
            <div class="text-left min-w-0">
              <p class="text-xs font-medium text-gray-900 dark:text-white truncate">{{ user.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 capitalize truncate">{{ user.role }}</p>
            </div>
          </div>
          <i :class="`fas fa-chevron-down text-xs text-gray-400 transition-transform duration-200 ${userMenuOpen ? 'rotate-180' : ''}`"></i>
        </button>

        <!-- Drop-up menu -->
        <div
          v-if="userMenuOpen"
          class="absolute bottom-full mb-2 left-1 right-1 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 z-10"
        >
          <div class="space-y-0.5 p-1">
            <Link
              :href="route('user.profile.edit')"
              @click="userMenuOpen = false"
              class="flex items-center gap-2 px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-700 dark:hover:text-green-300 rounded-md transition-colors"
            >
              <i class="fas fa-user-circle w-3.5"></i>Profile
            </Link>
            <form @submit.prevent="logout" class="w-full">
              <button
                type="submit"
                class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors"
              >
                <i class="fas fa-sign-out-alt w-3.5"></i>Sign Out
              </button>
            </form>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-64">
      <!-- Top bar -->
      <header class="h-16 bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between px-6 sticky top-0 z-40">
        <button
          @click="sidebarOpen = !sidebarOpen"
          class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800"
        >
          <i class="fas fa-bars text-lg"></i>
        </button>

        <div class="flex-1"></div>

        <div class="flex items-center gap-3">
          <!-- User badge -->
          <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-semibold rounded-full flex items-center gap-1.5">
            <i class="fas fa-user text-xs"></i>User
          </span>

          <!-- Notifications -->
          <button class="relative p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
            <i class="fas fa-bell text-lg"></i>
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
          </button>

          <!-- Theme toggle -->
          <button
            @click="toggleTheme"
            class="p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition-colors"
            :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
          >
            <i :class="`fas ${isDark ? 'fa-sun' : 'fa-moon'}`"></i>
          </button>
        </div>
      </header>

      <!-- Page content -->
      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

const sidebarOpen  = ref(false);
const userMenuOpen = ref(false);
const isDark       = ref(false);
const page         = usePage();

const user = computed(() => page.props.auth.user);

onMounted(() => {
  isDark.value = document.documentElement.classList.contains('dark');
});

const toggleTheme = () => {
  isDark.value = !isDark.value;
  document.documentElement.classList.toggle('dark', isDark.value);
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

const logout = () => {
  router.post(route('logout'));
};
</script>

<style scoped>
/* Layout styles */
</style>
