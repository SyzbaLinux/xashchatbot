<template>
  <AdminLayout>
    <div class="space-y-6">

      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Users</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">All registered accounts</p>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Admins</p>
          <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ stats.admins }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Users</p>
          <p class="text-2xl font-bold text-gray-700 dark:text-gray-300 mt-1">{{ stats.users }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Verified</p>
          <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ stats.verified }}</p>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div v-if="users.data.length > 0" class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-slate-700/50">
              <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Joined</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
              <tr
                v-for="u in users.data"
                :key="u.id"
                class="hover:bg-green-50/40 dark:hover:bg-green-900/10 transition-colors"
              >
                <td class="px-5 py-3.5 whitespace-nowrap">
                  <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full xash-gradient flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                      {{ u.name.charAt(0).toUpperCase() }}
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white">{{ u.name }}</span>
                  </div>
                </td>
                <td class="px-5 py-3.5 whitespace-nowrap text-gray-500 dark:text-gray-400">{{ u.email }}</td>
                <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs text-gray-500 dark:text-gray-400">{{ u.phone ?? '—' }}</td>
                <td class="px-5 py-3.5 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-0.5 rounded-full text-xs font-medium',
                    u.role === 'admin'
                      ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200'
                      : 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-400'
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
                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">{{ u.created_at }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="py-16 text-center">
          <i class="fas fa-users text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
          <p class="text-sm text-gray-500 dark:text-gray-400">No users yet</p>
        </div>

        <!-- Pagination -->
        <div v-if="users.last_page > 1" class="px-5 py-4 border-t border-gray-100 dark:border-slate-700 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
          <span>Page {{ users.current_page }} of {{ users.last_page }}</span>
          <div class="flex gap-2">
            <Link v-if="users.prev_page_url" :href="users.prev_page_url" class="px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded hover:bg-green-100 transition-colors">← Prev</Link>
            <Link v-if="users.next_page_url" :href="users.next_page_url" class="px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded hover:bg-green-100 transition-colors">Next →</Link>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
  users: any;
  stats: { total: number; admins: number; users: number; verified: number };
}>();
</script>
