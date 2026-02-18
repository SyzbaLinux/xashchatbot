<template>
  <AdminLayout>
    <div class="space-y-6">

      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Voucher Providers</h1>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
            Manage the voucher providers available to chatbot users
          </p>
        </div>
        <button
          @click="showAddModal = true"
          class="flex items-center gap-2 px-4 py-2 xash-gradient text-white text-sm font-medium rounded-lg shadow hover:opacity-90 transition-opacity"
        >
          <i class="fas fa-plus"></i>
          Add Provider
        </button>
      </div>

      <!-- Flash message -->
      <div
        v-if="$page.props.flash?.success"
        class="flex items-center gap-2 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg text-sm"
      >
        <i class="fas fa-check-circle"></i>
        {{ $page.props.flash.success }}
      </div>

      <!-- Providers grid -->
      <div v-if="providers.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="provider in providers"
          :key="provider.id"
          :class="[
            'bg-white dark:bg-slate-800 rounded-xl shadow border transition-all duration-200',
            provider.is_enabled
              ? 'border-green-300 dark:border-green-700 ring-1 ring-green-200 dark:ring-green-800'
              : 'border-gray-200 dark:border-slate-700'
          ]"
        >
          <div class="p-5">
            <!-- Header row -->
            <div class="flex items-start justify-between gap-3">
              <div class="flex items-center gap-3">
                <div :class="[
                  'w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0',
                  provider.is_enabled
                    ? 'xash-gradient text-white'
                    : 'bg-gray-100 dark:bg-slate-700 text-gray-400 dark:text-gray-500'
                ]">
                  <i class="fas fa-ticket-alt"></i>
                </div>
                <div>
                  <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ provider.name }}</p>
                  <p class="text-xs text-gray-400 dark:text-gray-500 font-mono mt-0.5">{{ provider.code }}</p>
                </div>
              </div>

              <!-- Enable/Disable toggle -->
              <button
                @click="toggle(provider)"
                :class="[
                  'relative inline-flex h-6 w-11 flex-shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none',
                  provider.is_enabled ? 'bg-green-500' : 'bg-gray-200 dark:bg-slate-600'
                ]"
                :title="provider.is_enabled ? 'Disable provider' : 'Enable provider'"
              >
                <span
                  :class="[
                    'pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform transition-transform duration-200',
                    provider.is_enabled ? 'translate-x-5' : 'translate-x-0'
                  ]"
                ></span>
              </button>
            </div>

            <!-- Footer row -->
            <div class="mt-4 flex items-center justify-between">
              <span :class="[
                'text-xs font-semibold px-2 py-0.5 rounded-full',
                provider.is_enabled
                  ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
                  : 'bg-gray-100 dark:bg-slate-700 text-gray-500 dark:text-gray-400'
              ]">
                {{ provider.is_enabled ? 'Enabled' : 'Disabled' }}
              </span>

              <div class="flex items-center gap-2">
                <button
                  @click="openEdit(provider)"
                  class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                  title="Edit provider"
                >
                  <i class="fas fa-pencil-alt text-xs"></i>
                </button>
                <button
                  @click="confirmDelete(provider)"
                  class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors"
                  title="Delete provider"
                >
                  <i class="fas fa-trash text-xs"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty state -->
      <div
        v-else
        class="flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-800 rounded-xl border border-dashed border-gray-300 dark:border-slate-600"
      >
        <div class="w-14 h-14 bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center mb-4">
          <i class="fas fa-ticket-alt text-2xl text-gray-400 dark:text-gray-500"></i>
        </div>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">No voucher providers yet</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Click "Add Provider" to get started</p>
      </div>

    </div>

    <!-- ── Add Provider Modal ─────────────────────────────── -->
    <Teleport to="body">
      <div
        v-if="showAddModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        @click.self="showAddModal = false"
      >
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl w-full max-w-md">
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-slate-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Add Voucher Provider</h2>
            <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <form @submit.prevent="submitAdd" class="p-5 space-y-4">
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Provider Name</label>
              <input
                v-model="addForm.name"
                type="text"
                placeholder="e.g. 1Voucher"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500"
                required
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Code <span class="text-gray-400 font-normal">(unique slug, no spaces)</span></label>
              <input
                v-model="addForm.code"
                type="text"
                placeholder="e.g. 1voucher"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500 font-mono"
                required
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
              <input
                v-model.number="addForm.sort_order"
                type="number"
                min="0"
                placeholder="0"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500"
              />
            </div>
            <div v-if="addError" class="text-xs text-red-600 dark:text-red-400">{{ addError }}</div>
            <div class="flex justify-end gap-3 pt-1">
              <button
                type="button"
                @click="showAddModal = false"
                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="addLoading"
                class="px-4 py-2 text-sm font-medium text-white xash-gradient rounded-lg hover:opacity-90 disabled:opacity-60 transition-opacity"
              >
                <i v-if="addLoading" class="fas fa-spinner fa-spin mr-1"></i>
                Add Provider
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- ── Edit Provider Modal ────────────────────────────── -->
    <Teleport to="body">
      <div
        v-if="showEditModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        @click.self="showEditModal = false"
      >
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl w-full max-w-md">
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-slate-700">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Edit Provider</h2>
            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
              <i class="fas fa-times"></i>
            </button>
          </div>
          <form @submit.prevent="submitEdit" class="p-5 space-y-4">
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Provider Name</label>
              <input
                v-model="editForm.name"
                type="text"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500"
                required
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Code</label>
              <input
                :value="editForm.code"
                type="text"
                class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-slate-600 rounded-lg bg-gray-50 dark:bg-slate-700/50 text-gray-500 dark:text-gray-400 font-mono cursor-not-allowed"
                disabled
              />
              <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Code cannot be changed after creation</p>
            </div>
            <div>
              <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
              <input
                v-model.number="editForm.sort_order"
                type="number"
                min="0"
                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-green-500"
              />
            </div>
            <div class="flex justify-end gap-3 pt-1">
              <button
                type="button"
                @click="showEditModal = false"
                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="editLoading"
                class="px-4 py-2 text-sm font-medium text-white xash-gradient rounded-lg hover:opacity-90 disabled:opacity-60 transition-opacity"
              >
                <i v-if="editLoading" class="fas fa-spinner fa-spin mr-1"></i>
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- ── Delete Confirm Modal ───────────────────────────── -->
    <Teleport to="body">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
        @click.self="showDeleteModal = false"
      >
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl w-full max-w-sm">
          <div class="p-6 text-center">
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
              <i class="fas fa-trash text-red-600 dark:text-red-400 text-lg"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Delete Provider</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
              Are you sure you want to delete <span class="font-semibold text-gray-700 dark:text-gray-300">{{ deleteTarget?.name }}</span>?
              This action cannot be undone.
            </p>
            <div class="flex gap-3 mt-5 justify-center">
              <button
                @click="showDeleteModal = false"
                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
              >
                Cancel
              </button>
              <button
                @click="submitDelete"
                :disabled="deleteLoading"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg disabled:opacity-60 transition-colors"
              >
                <i v-if="deleteLoading" class="fas fa-spinner fa-spin mr-1"></i>
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { router } from '@inertiajs/vue3';

interface Provider {
  id: number;
  code: string;
  name: string;
  is_enabled: boolean;
  sort_order: number;
}

const props = defineProps<{ providers: Provider[] }>();

// ── Add modal ──────────────────────────────────────────────
const showAddModal = ref(false);
const addLoading   = ref(false);
const addError     = ref('');
const addForm      = reactive({ name: '', code: '', sort_order: 0 });

const submitAdd = () => {
  addLoading.value = true;
  addError.value   = '';
  router.post(route('admin.voucher-providers.store'), addForm, {
    preserveScroll: true,
    onSuccess: () => {
      showAddModal.value  = false;
      addForm.name        = '';
      addForm.code        = '';
      addForm.sort_order  = 0;
    },
    onError: (errors) => {
      addError.value = Object.values(errors).flat().join(' ');
    },
    onFinish: () => { addLoading.value = false; },
  });
};

// ── Edit modal ─────────────────────────────────────────────
const showEditModal = ref(false);
const editLoading   = ref(false);
const editTarget    = ref<Provider | null>(null);
const editForm      = reactive({ name: '', code: '', sort_order: 0 });

const openEdit = (provider: Provider) => {
  editTarget.value       = provider;
  editForm.name          = provider.name;
  editForm.code          = provider.code;
  editForm.sort_order    = provider.sort_order;
  showEditModal.value    = true;
};

const submitEdit = () => {
  if (!editTarget.value) return;
  editLoading.value = true;
  router.put(route('admin.voucher-providers.update', editTarget.value.id), {
    name:       editForm.name,
    sort_order: editForm.sort_order,
  }, {
    preserveScroll: true,
    onSuccess: () => { showEditModal.value = false; },
    onFinish:  () => { editLoading.value = false; },
  });
};

// ── Toggle ─────────────────────────────────────────────────
const toggle = (provider: Provider) => {
  router.post(route('admin.voucher-providers.toggle', provider.id), {}, {
    preserveScroll: true,
  });
};

// ── Delete modal ───────────────────────────────────────────
const showDeleteModal = ref(false);
const deleteLoading   = ref(false);
const deleteTarget    = ref<Provider | null>(null);

const confirmDelete = (provider: Provider) => {
  deleteTarget.value    = provider;
  showDeleteModal.value = true;
};

const submitDelete = () => {
  if (!deleteTarget.value) return;
  deleteLoading.value = true;
  router.delete(route('admin.voucher-providers.destroy', deleteTarget.value.id), {
    preserveScroll: true,
    onSuccess: () => { showDeleteModal.value = false; },
    onFinish:  () => { deleteLoading.value = false; },
  });
};
</script>
