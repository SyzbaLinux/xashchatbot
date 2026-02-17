<template>
  <AdminLayout>
    <div class="space-y-6">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Transactions</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">All chatbot payment transactions</p>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Total</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Successful</p>
          <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">{{ stats.successful }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Failed</p>
          <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">{{ stats.failed }}</p>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-5 border border-gray-200 dark:border-slate-700">
          <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Volume</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${{ stats.volume.toFixed(2) }}</p>
        </div>
      </div>

      <!-- Table -->
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow border border-gray-200 dark:border-slate-700 overflow-hidden">
        <div v-if="transactions.data.length > 0" class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-slate-700/50">
              <tr>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Buyer</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Recipient</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Method</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
              <tr
                v-for="tx in transactions.data"
                :key="tx.id"
                class="hover:bg-green-50/40 dark:hover:bg-green-900/10 transition-colors"
              >
                <td class="px-5 py-3.5 whitespace-nowrap text-gray-700 dark:text-gray-300 font-mono text-xs">{{ tx.phone_number }}</td>
                <td class="px-5 py-3.5 whitespace-nowrap">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-full text-xs font-medium">
                    {{ tx.type }}
                  </span>
                </td>
                <td class="px-5 py-3.5 whitespace-nowrap text-gray-700 dark:text-gray-300 font-mono text-xs">{{ tx.recipient }}</td>
                <td class="px-5 py-3.5 whitespace-nowrap text-gray-900 dark:text-white font-semibold">{{ tx.currency }} {{ tx.amount.toFixed(2) }}</td>
                <td class="px-5 py-3.5 whitespace-nowrap text-gray-500 dark:text-gray-400 text-xs">{{ tx.payment_method }}</td>
                <td class="px-5 py-3.5 whitespace-nowrap">
                  <span :class="[
                    'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium',
                    tx.status === 'success'
                      ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200'
                      : tx.status === 'failed'
                        ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200'
                        : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200'
                  ]">
                    {{ tx.status }}
                  </span>
                </td>
                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">{{ tx.created_at }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-else class="py-16 text-center">
          <i class="fas fa-receipt text-gray-300 dark:text-gray-600 text-4xl mb-3"></i>
          <p class="text-sm text-gray-500 dark:text-gray-400">No transactions yet</p>
        </div>

        <!-- Pagination -->
        <div v-if="transactions.last_page > 1" class="px-5 py-4 border-t border-gray-100 dark:border-slate-700 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
          <span>Page {{ transactions.current_page }} of {{ transactions.last_page }}</span>
          <div class="flex gap-2">
            <Link
              v-if="transactions.prev_page_url"
              :href="transactions.prev_page_url"
              class="px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors"
            >
              ← Prev
            </Link>
            <Link
              v-if="transactions.next_page_url"
              :href="transactions.next_page_url"
              class="px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors"
            >
              Next →
            </Link>
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
  transactions: any;
  stats: { total: number; successful: number; failed: number; volume: number };
}>();
</script>
