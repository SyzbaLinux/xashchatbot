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
          <div class="hidden sm:block text-5xl opacity-20">
            <i class="fas fa-comments-dollar"></i>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Transactions -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Transactions</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total_transactions }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-exchange-alt text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Successful -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Successful</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.successful_transactions }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Failed -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Failed</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.failed_transactions }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Total Spent -->
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Spent</p>
              <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">${{ stats.total_spent.toFixed(2) }}</p>
            </div>
            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-wallet text-emerald-600 dark:text-emerald-400 text-xl"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Quick Actions -->
        <div class="lg:col-span-1">
          <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
              <i class="fas fa-bolt text-emerald-600 dark:text-emerald-400"></i>
              Quick Actions
            </h2>
            <div class="space-y-2">
              <a
                href="https://wa.me/263774916256"
                target="_blank"
                class="flex items-center w-full px-4 py-2.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 rounded-lg font-medium hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors text-sm"
              >
                <i class="fas fa-mobile-alt mr-2"></i>Buy Airtime via WhatsApp
              </a>
              <a
                href="https://wa.me/263774916256"
                target="_blank"
                class="flex items-center w-full px-4 py-2.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg font-medium hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors text-sm"
              >
                <i class="fas fa-wifi mr-2"></i>Buy Data Bundles
              </a>
              <a
                href="https://wa.me/263774916256"
                target="_blank"
                class="flex items-center w-full px-4 py-2.5 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-lg font-medium hover:bg-purple-100 dark:hover:bg-purple-900/40 transition-colors text-sm"
              >
                <i class="fas fa-file-invoice-dollar mr-2"></i>Pay Utility Bills
              </a>
            </div>
          </div>
        </div>

        <!-- Recent Transactions -->
        <div class="lg:col-span-2">
          <div class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
              <i class="fas fa-history text-blue-600 dark:text-blue-400"></i>
              Recent Transactions
            </h2>

            <div v-if="recent_transactions.length > 0" class="space-y-3">
              <div
                v-for="tx in recent_transactions"
                :key="tx.id"
                class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-slate-700/50"
              >
                <div class="flex items-center gap-3">
                  <div
                    class="w-9 h-9 rounded-full flex items-center justify-center text-sm"
                    :class="tx.status === 'success'
                      ? 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'
                      : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'"
                  >
                    <i :class="tx.status === 'success' ? 'fas fa-check' : 'fas fa-times'"></i>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ tx.type }} → {{ tx.recipient }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ tx.created_at }} · {{ tx.method }}</p>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ tx.currency }} {{ tx.amount.toFixed(2) }}
                  </p>
                  <span
                    class="text-xs px-2 py-0.5 rounded-full"
                    :class="tx.status === 'success'
                      ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300'
                      : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300'"
                  >
                    {{ tx.status }}
                  </span>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8">
              <i class="fas fa-receipt text-gray-400 dark:text-gray-500 text-4xl mb-3"></i>
              <p class="text-gray-600 dark:text-gray-400">No transactions yet</p>
              <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">
                Start by buying airtime or data bundles via WhatsApp
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayout>
</template>

<script setup lang="ts">
import UserLayout from '@/Layouts/UserLayout.vue';

interface Stats {
  total_transactions: number;
  successful_transactions: number;
  failed_transactions: number;
  total_spent: number;
}

interface User {
  name: string;
  email: string;
  role: string;
  created_at: string;
}

interface Transaction {
  id: number;
  type: string;
  recipient: string;
  amount: number;
  currency: string;
  status: string;
  method: string;
  created_at: string;
}

defineProps<{
  user: User;
  stats: Stats;
  recent_transactions: Transaction[];
}>();
</script>
