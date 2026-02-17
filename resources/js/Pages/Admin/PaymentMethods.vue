<template>
  <AdminLayout>
    <div class="space-y-6">

      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payment Methods</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Enable or disable payment options available to chatbot users</p>
      </div>

      <!-- Flash message -->
      <div
        v-if="$page.props.flash?.success"
        class="flex items-center gap-2 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg text-sm"
      >
        <i class="fas fa-check-circle"></i>
        {{ $page.props.flash.success }}
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="method in methods"
          :key="method.id"
          :class="[
            'bg-white dark:bg-slate-800 rounded-xl shadow border transition-all duration-200',
            method.is_enabled
              ? 'border-green-300 dark:border-green-700 ring-1 ring-green-200 dark:ring-green-800'
              : 'border-gray-200 dark:border-slate-700'
          ]"
        >
          <div class="p-5">
            <!-- Header -->
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3">
                <div :class="[
                  'w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0',
                  method.is_enabled
                    ? 'xash-gradient text-white'
                    : 'bg-gray-100 dark:bg-slate-700 text-gray-400 dark:text-gray-500'
                ]">
                  <i :class="`fas ${methodIcon(method.code)}`"></i>
                </div>
                <div>
                  <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ method.name }}</p>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ method.description }}</p>
                </div>
              </div>

              <!-- Toggle -->
              <button
                @click="toggle(method)"
                :class="[
                  'relative inline-flex h-6 w-11 flex-shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none',
                  method.is_enabled ? 'bg-green-500' : 'bg-gray-200 dark:bg-slate-600'
                ]"
              >
                <span
                  :class="[
                    'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform transition-transform duration-200',
                    method.is_enabled ? 'translate-x-5' : 'translate-x-0'
                  ]"
                ></span>
              </button>
            </div>

            <!-- Countries & status -->
            <div class="mt-4 flex items-center justify-between">
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="country in method.countries"
                  :key="country"
                  class="text-xs px-1.5 py-0.5 bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-400 rounded font-mono"
                >
                  {{ country }}
                </span>
              </div>
              <span :class="[
                'text-xs font-semibold px-2 py-0.5 rounded-full',
                method.is_enabled
                  ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
                  : 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400'
              ]">
                {{ method.is_enabled ? 'Enabled' : 'Disabled' }}
              </span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';

interface Method {
  id: number;
  code: string;
  name: string;
  description: string;
  countries: string[];
  is_enabled: boolean;
  sort_order: number;
}

const props = defineProps<{ methods: Method[] }>();

const methodIcon = (code: string): string => {
  const icons: Record<string, string> = {
    voucher:     'fa-ticket-alt',
    ecocash:     'fa-mobile-alt',
    onemoney:    'fa-sim-card',
    innbucks:    'fa-wallet',
    mpesa:       'fa-mobile-alt',
    orangemoney: 'fa-circle',
  };
  return icons[code] ?? 'fa-credit-card';
};

const toggle = (method: Method) => {
  router.post(route('admin.payment-methods.toggle', method.id), {}, {
    preserveScroll: true,
  });
};
</script>
