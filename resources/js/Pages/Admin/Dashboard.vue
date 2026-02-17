<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Welcome Section -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 rounded-xl shadow-lg p-6 sm:p-8 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl sm:text-4xl font-bold">Admin Panel</h1>
            <p class="text-indigo-50 mt-2">Welcome, {{ user.name }}</p>
          </div>
          <div class="hidden sm:block text-5xl">
            <i class="fas fa-crown opacity-20"></i>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Users -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Users</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total_users }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Admins -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Admins</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total_admins }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-user-secret text-purple-600 dark:text-purple-400 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Regular Users -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Regular Users</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total_regular_users }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-user text-green-600 dark:text-green-400 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Verified Users -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Verified</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.verified_users }}</p>
            </div>
            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Unverified Users -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Unverified</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.unverified_users }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-exclamation-circle text-orange-600 dark:text-orange-400 text-xl"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Admin Actions -->
        <div class="lg:col-span-1">
          <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
              <i class="fas fa-tools text-indigo-600 dark:text-indigo-400"></i>
              Admin Actions
            </h2>
            <div class="space-y-2">
              <button class="w-full px-4 py-2.5 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 rounded-lg font-medium hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors text-sm">
                <i class="fas fa-user-plus mr-2"></i>Add User
              </button>
              <button class="w-full px-4 py-2.5 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-lg font-medium hover:bg-purple-100 dark:hover:bg-purple-900/40 transition-colors text-sm">
                <i class="fas fa-sliders-h mr-2"></i>Settings
              </button>
              <button class="w-full px-4 py-2.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 rounded-lg font-medium hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors text-sm">
                <i class="fas fa-file-csv mr-2"></i>Reports
              </button>
              <button class="w-full px-4 py-2.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg font-medium hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors text-sm">
                <i class="fas fa-list mr-2"></i>View All Users
              </button>
            </div>
          </div>
        </div>

        <!-- Recent Users -->
        <div class="lg:col-span-2">
          <div class="bg-white dark:bg-slate-800 rounded-lg shadow border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-user-clock text-blue-600 dark:text-blue-400"></i>
                Recent Users
              </h2>
            </div>

            <div v-if="recent_users.length > 0" class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50 border-t border-gray-200 dark:border-slate-700">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">Joined</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                  <tr v-for="u in recent_users" :key="u.id" class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center text-white text-sm font-semibold">
                          {{ u.name.charAt(0).toUpperCase() }}
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ u.name }}</span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ u.email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="[
                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        u.role === 'admin'
                          ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-200'
                          : 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200'
                      ]">
                        {{ u.role }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span v-if="u.verified" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                        <i class="fas fa-check-circle mr-1"></i>
                        Verified
                      </span>
                      <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                        <i class="fas fa-clock mr-1"></i>
                        Pending
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">{{ u.created_at }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-else class="p-8 text-center">
              <i class="fas fa-inbox text-gray-400 dark:text-gray-500 text-4xl mb-3"></i>
              <p class="text-gray-600 dark:text-gray-400">No users yet</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

interface Stats {
  total_users: number;
  total_admins: number;
  total_regular_users: number;
  verified_users: number;
  unverified_users: number;
}

interface User {
  name: string;
  email: string;
  role: string;
  id?: number;
  created_at?: string;
  verified?: boolean;
}

defineProps<{
  user: User;
  stats: Stats;
  recent_users: User[];
}>();
</script>

<style scoped>
/* Page-specific styles */
</style>
