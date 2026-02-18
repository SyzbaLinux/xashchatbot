<template>
  <AdminLayout>
    <div class="space-y-6">

      <!-- Header -->
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Transactions</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">All chatbot payment transactions</p>
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
          <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">ZAR {{ Number(stats.volume).toFixed(2) }}</p>
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
                <th class="px-5 py-3"></th>
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
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 rounded-full text-xs font-medium capitalize">
                    {{ tx.type }}
                  </span>
                </td>
                <td class="px-5 py-3.5 whitespace-nowrap text-gray-700 dark:text-gray-300 font-mono text-xs">{{ tx.recipient }}</td>
                <td class="px-5 py-3.5 whitespace-nowrap text-gray-900 dark:text-white font-semibold">
                  {{ tx.currency }} {{ Number(tx.amount).toFixed(2) }}
                </td>
                <td class="px-5 py-3.5 whitespace-nowrap">
                  <span class="inline-flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <i :class="`fas ${methodIcon(tx.payment_method)} text-gray-400`"></i>
                    {{ tx.payment_method }}
                  </span>
                </td>
                <td class="px-5 py-3.5 whitespace-nowrap">
                  <span :class="statusClass(tx.status)">
                    <i :class="statusIcon(tx.status)"></i>
                    {{ tx.status }}
                  </span>
                </td>
                <td class="px-5 py-3.5 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">{{ tx.created_at }}</td>
                <td class="px-5 py-3.5 whitespace-nowrap text-right">
                  <button
                    @click="openDetail(tx)"
                    class="text-xs text-green-600 dark:text-green-400 hover:underline font-medium"
                  >
                    View
                  </button>
                </td>
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
            >← Prev</Link>
            <Link
              v-if="transactions.next_page_url"
              :href="transactions.next_page_url"
              class="px-3 py-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded hover:bg-green-100 dark:hover:bg-green-900/40 transition-colors"
            >Next →</Link>
          </div>
        </div>
      </div>

    </div>

    <!-- ── Transaction Detail Drawer ──────────────────────── -->
    <Teleport to="body">
      <div
        v-if="selected"
        class="fixed inset-0 z-50 flex justify-end"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/40" @click="selected = null"></div>

        <!-- Panel -->
        <div class="relative w-full max-w-md bg-white dark:bg-slate-900 h-full overflow-y-auto shadow-2xl flex flex-col">

          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-slate-700 sticky top-0 bg-white dark:bg-slate-900 z-10">
            <div class="flex items-center gap-3">
              <div :class="[
                'w-9 h-9 rounded-lg flex items-center justify-center',
                selected.status === 'success' ? 'bg-green-100 dark:bg-green-900/30' :
                selected.status === 'failed'  ? 'bg-red-100 dark:bg-red-900/30' :
                                                'bg-yellow-100 dark:bg-yellow-900/30'
              ]">
                <i :class="[
                  'fas',
                  selected.status === 'success' ? 'fa-check text-green-600 dark:text-green-400' :
                  selected.status === 'failed'  ? 'fa-times text-red-600 dark:text-red-400' :
                                                  'fa-clock text-yellow-600 dark:text-yellow-400'
                ]"></i>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Transaction #{{ selected.id }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ selected.created_at }}</p>
              </div>
            </div>
            <button @click="selected = null" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
              <i class="fas fa-times"></i>
            </button>
          </div>

          <!-- Body -->
          <div class="p-6 space-y-6 flex-1">

            <!-- Amount highlight -->
            <div class="text-center py-4 bg-gray-50 dark:bg-slate-800 rounded-xl">
              <p class="text-xs text-gray-400 dark:text-gray-500 mb-1 uppercase tracking-wider">Amount</p>
              <p class="text-4xl font-bold text-gray-900 dark:text-white">
                {{ selected.currency }} {{ Number(selected.amount).toFixed(2) }}
              </p>
              <span :class="['mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold', statusClass(selected.status)]">
                <i :class="statusIcon(selected.status)"></i>
                {{ selected.status }}
              </span>
            </div>

            <!-- Details grid -->
            <div class="space-y-3">
              <DetailRow label="Buyer" :value="selected.phone_number" mono />
              <DetailRow label="Recipient" :value="selected.recipient" mono />
              <DetailRow label="Type" :value="selected.type" />
              <DetailRow label="Payment Method">
                <span class="flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300">
                  <i :class="`fas ${methodIcon(selected.payment_method)} text-gray-400 text-xs`"></i>
                  {{ selected.payment_method }}
                </span>
              </DetailRow>
              <DetailRow v-if="selected.voucher_code" label="Voucher Code" :value="selected.voucher_code" mono />
            </div>

            <!-- Response data -->
            <div v-if="selected.response_data && Object.keys(selected.response_data).length">
              <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Details</p>
              <div class="space-y-2">
                <div
                  v-for="(val, key) in selected.response_data"
                  :key="key"
                  class="flex justify-between items-start gap-4 text-sm"
                >
                  <span class="text-gray-500 dark:text-gray-400 capitalize shrink-0">{{ formatKey(String(key)) }}</span>
                  <span class="text-gray-800 dark:text-gray-200 text-right break-all font-mono text-xs">{{ val }}</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </Teleport>

  </AdminLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

interface Transaction {
  id: number;
  phone_number: string;
  type: string;
  recipient: string;
  amount: number;
  currency: string;
  payment_method: string;
  voucher_code: string | null;
  status: string;
  response_data: Record<string, unknown> | null;
  created_at: string;
}

defineProps<{
  transactions: { data: Transaction[]; last_page: number; current_page: number; prev_page_url: string | null; next_page_url: string | null };
  stats: { total: number; successful: number; failed: number; volume: number };
}>();

const selected = ref<Transaction | null>(null);

const openDetail = (tx: Transaction) => { selected.value = tx; };

const statusClass = (status: string) =>
  status === 'success'
    ? 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200'
    : status === 'failed'
      ? 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200'
      : 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200';

const statusIcon = (status: string) =>
  status === 'success' ? 'fas fa-check-circle' :
  status === 'failed'  ? 'fas fa-times-circle' : 'fas fa-clock';

const methodIcon = (code: string) => {
  const icons: Record<string, string> = {
    voucher:     'fa-ticket-alt',
    ecocash:     'fa-mobile-alt',
    onemoney:    'fa-sim-card',
    innbucks:    'fa-wallet',
    mpesa:       'fa-mobile-alt',
    orangemoney: 'fa-circle',
    express:     'fa-credit-card',
  };
  return icons[code] ?? 'fa-credit-card';
};

const formatKey = (key: string) => key.replace(/_/g, ' ');
</script>

<!-- Inline component for detail rows -->
<script lang="ts">
import { defineComponent, h } from 'vue';

const DetailRow = defineComponent({
  props: {
    label: { type: String, required: true },
    value: { type: String, default: '' },
    mono:  { type: Boolean, default: false },
  },
  setup(props, { slots }) {
    return () => h('div', { class: 'flex justify-between items-start gap-4' }, [
      h('span', { class: 'text-xs text-gray-500 dark:text-gray-400 shrink-0 pt-0.5' }, props.label),
      slots.default
        ? h('div', { class: 'text-right' }, slots.default())
        : h('span', {
            class: `text-sm text-right break-all ${props.mono ? 'font-mono text-xs text-gray-700 dark:text-gray-300' : 'text-gray-800 dark:text-gray-200'}`,
          }, props.value),
    ]);
  },
});

export { DetailRow };
</script>
