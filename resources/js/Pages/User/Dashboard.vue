<template>
  <UserLayout>
    <div class="space-y-6">
      <!-- Welcome Section -->
      <div class="bg-gradient-to-r from-emerald-600 to-green-600 dark:from-emerald-500 dark:to-green-500 rounded-xl shadow-lg p-6 sm:p-8 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl sm:text-4xl font-bold">Welcome back, {{ user.name }}!</h1>
            <p class="text-emerald-50 mt-2">Member since {{ user.created_at }}</p>
          </div>
          <div class="hidden sm:block text-5xl">
            <i class="fas fa-user-circle opacity-20"></i>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Tasks -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Tasks</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total_tasks }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-tasks text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Completed Tasks -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Completed</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.completed_tasks }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Pending Tasks -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Pending</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.pending_tasks }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-hourglass-half text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Total Errands -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Errands</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total_errands }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-shopping-bags text-purple-600 dark:text-purple-400 text-xl"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="lg:col-span-1">
          <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
              <i class="fas fa-lightning-bolt text-emerald-600 dark:text-emerald-400"></i>
              Quick Actions
            </h2>
            <div class="space-y-2">
              <button class="w-full px-4 py-2.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 rounded-lg font-medium hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors text-sm">
                <i class="fas fa-plus mr-2"></i>New Task
              </button>
              <button class="w-full px-4 py-2.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg font-medium hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors text-sm">
                <i class="fas fa-box mr-2"></i>New Errand
              </button>
              <button class="w-full px-4 py-2.5 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-lg font-medium hover:bg-purple-100 dark:hover:bg-purple-900/40 transition-colors text-sm">
                <i class="fas fa-calendar mr-2"></i>Schedule
              </button>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-2">
          <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
              <i class="fas fa-history text-blue-600 dark:text-blue-400"></i>
              Recent Activity
            </h2>

            <div v-if="recent_activity.length > 0" class="space-y-4">
              <div v-for="activity in recent_activity" :key="activity.id" class="flex items-start gap-4 p-3 rounded-lg bg-gray-50 dark:bg-slate-700/50">
                <i :class="`fas ${activity.icon} text-emerald-600 dark:text-emerald-400 mt-1`"></i>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">{{ activity.title }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ activity.timestamp }}</p>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8">
              <i class="fas fa-inbox text-gray-400 dark:text-gray-500 text-4xl mb-3"></i>
              <p class="text-gray-600 dark:text-gray-400">No recent activity yet</p>
              <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Start by creating a new task or errand</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';
import UserLayout from '@/Layouts/UserLayout.vue';

interface Stats {
  total_tasks: number;
  completed_tasks: number;
  pending_tasks: number;
  total_errands: number;
}

interface User {
  name: string;
  email: string;
  role: string;
  created_at: string;
}

defineProps<{
  user: User;
  stats: Stats;
  recent_activity: any[];
}>();
</script>

<style scoped>
/* Page-specific styles */
</style>
