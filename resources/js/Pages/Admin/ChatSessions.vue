<template>
  <AdminLayout>
    <div class="space-y-6">

      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat Sessions</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">WhatsApp chatbot conversation states</p>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Active</p>
          <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ stats.active }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Idle</p>
          <p class="text-2xl font-bold text-gray-500 dark:text-gray-400 mt-1">{{ stats.idle }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Expired</p>
          <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">{{ stats.expired }}</p>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div v-if="sessions.data.length > 0" class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-slate-700/50">
              <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Step</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expires</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Activity</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
              <tr
                v-for="s in sessions.data"
                :key="s.id"
                class="hover:bg-green-50/40 dark:hover:bg-green-900/10 transition-colors"
              >
                <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs text-gray-700 dark:text-gray-300">{{ s.phone_number }}</td>
                <td class="px-5 py-3.5 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-0.5 rounded-full text-xs font-medium',
                    s.step === 'idle'
                      ? 'bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-400'
                      : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200'
                  ]">
                    {{ s.step }}
                  </span>
                </td>
                <td class="px-5 py-3.5 text-xs text-gray-500 dark:text-gray-400 max-w-[200px] truncate">
                  {{ s.session_data && Object.keys(s.session_data).length ? JSON.stringify(s.session_data) : '—' }}
                </td>
                <td class="px-5 py-3.5 whitespace-nowrap text-xs">
                  <span :class="s.is_expired ? 'text-red-500 dark:text-red-400' : 'text-gray-500 dark:text-gray-400'">
                    {{ s.expires_at ?? '—' }}
                  </span>
                </td>
                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">{{ s.updated_at }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="py-16 text-center">
          <i class="fas fa-comments text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
          <p class="text-sm text-gray-500 dark:text-gray-400">No chat sessions yet</p>
        </div>

        <!-- Pagination -->
        <div v-if="sessions.last_page > 1" class="px-5 py-4 border-t border-gray-100 dark:border-slate-700 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
          <span>Page {{ sessions.current_page }} of {{ sessions.last_page }}</span>
          <div class="flex gap-2">
            <Link v-if="sessions.prev_page_url" :href="sessions.prev_page_url" class="px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded hover:bg-green-100 transition-colors">← Prev</Link>
            <Link v-if="sessions.next_page_url" :href="sessions.next_page_url" class="px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded hover:bg-green-100 transition-colors">Next →</Link>
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
  sessions: any;
  stats: { total: number; active: number; idle: number; expired: number };
}>();
</script>
