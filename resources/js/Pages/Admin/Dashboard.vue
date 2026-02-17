<template>
  <AdminLayout>
    <div class="space-y-6">

      <!-- Welcome hero -->
      <div class="xash-gradient rounded-xl shadow-lg p-6 sm:p-8 text-white">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl sm:text-4xl font-bold">Admin Panel</h1>
            <p class="text-green-100 mt-1">Welcome back, {{ user.name }}</p>
          </div>
          <div class="hidden sm:block text-6xl opacity-15">
            <i class="fas fa-shield-alt"></i>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Users</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total_users }}</p>
            </div>
            <div class="w-11 h-11 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-users text-green-600 dark:text-green-400"></i>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Admins</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total_admins }}</p>
            </div>
            <div class="w-11 h-11 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-user-shield text-green-700 dark:text-green-300"></i>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Regular Users</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total_regular_users }}</p>
            </div>
            <div class="w-11 h-11 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-user text-green-600 dark:text-green-400"></i>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Verified</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.verified_users }}</p>
            </div>
            <div class="w-11 h-11 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Unverified</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.unverified_users }}</p>
            </div>
            <div class="w-11 h-11 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-exclamation-circle text-yellow-600 dark:text-yellow-400"></i>
            </div>
          </div>
        </div>

      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Quick actions -->
        <div class="lg:col-span-1">
          <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700 h-full">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
              <i class="fas fa-bolt text-green-600 dark:text-green-400"></i>
              Quick Actions
            </h2>
            <div class="space-y-2">
              <Link
                :href="route('admin.transactions')"
                class="flex items-center w-full px-4 py-2.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg font-medium hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors text-sm"
              >
                <i class="fas fa-exchange-alt mr-2 w-4"></i>View Transactions
              </Link>
              <Link
                :href="route('admin.payment-methods')"
                class="flex items-center w-full px-4 py-2.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg font-medium hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors text-sm"
              >
                <i class="fas fa-credit-card mr-2 w-4"></i>Payment Methods
              </Link>
              <Link
                :href="route('admin.chat-sessions')"
                class="flex items-center w-full px-4 py-2.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg font-medium hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors text-sm"
              >
                <i class="fas fa-comments mr-2 w-4"></i>Chat Sessions
              </Link>
              <Link
                :href="route('admin.users')"
                class="flex items-center w-full px-4 py-2.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg font-medium hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors text-sm"
              >
                <i class="fas fa-users mr-2 w-4"></i>Manage Users
              </Link>
            </div>
          </div>
        </div>

        <!-- Recent users -->
        <div class="lg:col-span-2">
          <div class="bg-white dark:bg-slate-800 rounded-lg shadow border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between">
              <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-user-clock text-green-600 dark:text-green-400"></i>
                Recent Users
              </h2>
              <Link
                :href="route('admin.users')"
                class="text-xs text-green-600 dark:text-green-400 hover:underline font-medium"
              >
                View all →
              </Link>
            </div>

            <div v-if="recent_users.length > 0" class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                  <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Joined</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                  <tr
                    v-for="u in recent_users"
                    :key="u.id"
                    class="hover:bg-green-50/50 dark:hover:bg-green-900/10 transition-colors"
                  >
                    <td class="px-5 py-3.5 whitespace-nowrap">
                      <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full xash-gradient flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                          {{ u.name.charAt(0).toUpperCase() }}
                        </div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ u.name }}</span>
                      </div>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ u.email }}</td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                      <span :class="[
                        'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                        u.role === 'admin'
                          ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200'
                          : 'bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300'
                      ]">
                        {{ u.role }}
                      </span>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap">
                      <span v-if="u.verified" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                        <i class="fas fa-check-circle text-xs"></i>Verified
                      </span>
                      <span v-else class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                        <i class="fas fa-clock text-xs"></i>Pending
                      </span>
                    </td>
                    <td class="px-5 py-3.5 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ u.created_at }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-else class="py-12 text-center">
              <i class="fas fa-inbox text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
              <p class="text-sm text-gray-500 dark:text-gray-400">No users yet</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

interface Stats {
  total_users: number;
  total_admins: number;
  total_regular_users: number;
  verified_users: number;
  unverified_users: number;
}

interface User {
  id?: number;
  name: string;
  email: string;
  role: string;
  created_at?: string;
  verified?: boolean;
}

defineProps<{
  user: User;
  stats: Stats;
  recent_users: User[];
}>();
</script>
