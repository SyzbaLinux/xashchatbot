<template>
  <UserLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Profile Settings</h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your account information and security</p>
        </div>
      </div>

      <!-- Tab Navigation -->
      <div class="flex gap-2 border-b border-gray-200 dark:border-slate-700">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-4 py-3 font-medium border-b-2 transition-colors -mb-px',
            activeTab === tab.id
              ? 'border-emerald-600 dark:border-emerald-400 text-emerald-600 dark:text-emerald-400'
              : 'border-transparent text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100'
          ]"
        >
          <i :class="`fas ${tab.icon} mr-2`"></i>
          {{ tab.label }}
        </button>
      </div>

      <!-- Personal Information Tab -->
      <div v-show="activeTab === 'personal'" class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Personal Information</h2>

        <form @submit.prevent="submitPersonal" class="space-y-6">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
            <input
              id="name"
              v-model="personalForm.name"
              type="text"
              class="w-full px-4 py-2.5 border rounded-lg transition-all duration-200 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:focus:ring-emerald-400"
              required
            />
            <p v-if="personalForm.errors.name" class="text-sm text-red-600 dark:text-red-400 mt-2">
              <i class="fas fa-exclamation-circle mr-1.5"></i>
              {{ personalForm.errors.name }}
            </p>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
            <input
              id="email"
              v-model="personalForm.email"
              type="email"
              class="w-full px-4 py-2.5 border rounded-lg transition-all duration-200 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:focus:ring-emerald-400"
              required
            />
            <p v-if="personalForm.errors.email" class="text-sm text-red-600 dark:text-red-400 mt-2">
              <i class="fas fa-exclamation-circle mr-1.5"></i>
              {{ personalForm.errors.email }}
            </p>
          </div>

          <!-- Member Since -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Member Since</label>
            <div class="px-4 py-2.5 bg-gray-50 dark:bg-slate-700/50 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-400">
              {{ user.created_at }}
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="personalForm.processing"
              class="px-6 py-2.5 bg-emerald-600 dark:bg-emerald-500 text-white font-semibold rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 disabled:opacity-50 transition-all duration-300"
            >
              <i v-if="personalForm.processing" class="fas fa-spinner animate-spin mr-2"></i>
              {{ personalForm.processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Password Tab -->
      <div v-show="activeTab === 'password'" class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Change Password</h2>

        <form @submit.prevent="submitPassword" class="space-y-6 max-w-md">
          <!-- Current Password -->
          <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
            <input
              id="current_password"
              v-model="passwordForm.current_password"
              type="password"
              class="w-full px-4 py-2.5 border rounded-lg transition-all duration-200 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:focus:ring-emerald-400"
              required
            />
            <p v-if="passwordForm.errors.current_password" class="text-sm text-red-600 dark:text-red-400 mt-2">
              <i class="fas fa-exclamation-circle mr-1.5"></i>
              {{ passwordForm.errors.current_password }}
            </p>
          </div>

          <!-- New Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
            <input
              id="password"
              v-model="passwordForm.password"
              type="password"
              class="w-full px-4 py-2.5 border rounded-lg transition-all duration-200 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:focus:ring-emerald-400"
              required
            />
            <p v-if="passwordForm.errors.password" class="text-sm text-red-600 dark:text-red-400 mt-2">
              <i class="fas fa-exclamation-circle mr-1.5"></i>
              {{ passwordForm.errors.password }}
            </p>
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
            <input
              id="password_confirmation"
              v-model="passwordForm.password_confirmation"
              type="password"
              class="w-full px-4 py-2.5 border rounded-lg transition-all duration-200 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-emerald-500 focus:border-transparent dark:focus:ring-emerald-400"
              required
            />
            <p v-if="passwordForm.errors.password_confirmation" class="text-sm text-red-600 dark:text-red-400 mt-2">
              <i class="fas fa-exclamation-circle mr-1.5"></i>
              {{ passwordForm.errors.password_confirmation }}
            </p>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="passwordForm.processing"
              class="px-6 py-2.5 bg-emerald-600 dark:bg-emerald-500 text-white font-semibold rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 disabled:opacity-50 transition-all duration-300"
            >
              <i v-if="passwordForm.processing" class="fas fa-spinner animate-spin mr-2"></i>
              {{ passwordForm.processing ? 'Updating...' : 'Update Password' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Two-Factor Authentication Tab -->
      <div v-show="activeTab === 'security'" class="bg-white dark:bg-slate-800 rounded-lg shadow p-6 border border-gray-200 dark:border-slate-700">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Two-Factor Authentication</h2>

        <div v-if="!twoFactorEnabled" class="space-y-6">
          <div class="p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
            <p class="text-blue-700 dark:text-blue-300 flex items-start gap-2">
              <i class="fas fa-shield-alt mt-0.5 flex-shrink-0"></i>
              <span>Two-factor authentication adds an extra layer of security to your account. You'll need to enter a code from your authenticator app in addition to your password when signing in.</span>
            </p>
          </div>

          <button
            @click="enableTwoFactorModal = true"
            class="px-6 py-3 bg-emerald-600 dark:bg-emerald-500 text-white font-semibold rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 transition-all duration-300 flex items-center gap-2"
          >
            <i class="fas fa-shield-check"></i>
            Enable Two-Factor Authentication
          </button>
        </div>

        <div v-else class="space-y-6">
          <div class="p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
            <p class="text-green-700 dark:text-green-300 flex items-center gap-2">
              <i class="fas fa-check-circle"></i>
              <span>Two-factor authentication is enabled on your account.</span>
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Recovery Codes -->
            <div class="p-4 border border-gray-300 dark:border-gray-600 rounded-lg">
              <h3 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <i class="fas fa-key"></i>
                Recovery Codes
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Keep these codes in a safe place. You can use them to access your account if you lose access to your authenticator.
              </p>
              <button
                @click="regenerateRecoveryCodes"
                :disabled="regeneratingCodes"
                class="w-full px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 disabled:opacity-50 transition-colors text-sm"
              >
                <i v-if="regeneratingCodes" class="fas fa-spinner animate-spin mr-2"></i>
                {{ regeneratingCodes ? 'Regenerating...' : 'Regenerate Codes' }}
              </button>
            </div>

            <!-- Disable 2FA -->
            <div class="p-4 border border-red-300 dark:border-red-600 rounded-lg">
              <h3 class="font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <i class="fas fa-ban"></i>
                Disable 2FA
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Remove two-factor authentication from your account. This will reduce your account security.
              </p>
              <button
                @click="disableConfirmModal = true"
                class="w-full px-4 py-2 bg-red-600 dark:bg-red-500 text-white font-medium rounded-lg hover:bg-red-700 dark:hover:bg-red-600 transition-colors text-sm"
              >
                <i class="fas fa-times mr-2"></i>
                Disable 2FA
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Disable 2FA Confirmation Modal -->
    <Teleport to="body" v-if="disableConfirmModal">
      <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl max-w-md w-full p-6 border border-gray-200 dark:border-slate-700">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Disable Two-Factor Authentication?</h3>
          <p class="text-gray-600 dark:text-gray-400 mb-6">
            Are you sure you want to disable two-factor authentication? Your account will be less secure.
          </p>

          <form @submit.prevent="disableTwoFactor" class="space-y-4">
            <div>
              <label for="disable_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Confirm with your password:
              </label>
              <input
                id="disable_password"
                v-model="disableForm.password"
                type="password"
                class="w-full px-4 py-2.5 border rounded-lg bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-red-500"
                required
              />
              <p v-if="disableForm.errors.password" class="text-sm text-red-600 dark:text-red-400 mt-2">
                {{ disableForm.errors.password }}
              </p>
            </div>

            <div class="flex gap-3">
              <button
                type="button"
                @click="disableConfirmModal = false"
                class="flex-1 px-4 py-2.5 bg-gray-300 dark:bg-slate-700 text-gray-900 dark:text-white font-medium rounded-lg hover:bg-gray-400 dark:hover:bg-slate-600 transition-colors"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="disableForm.processing"
                class="flex-1 px-4 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors"
              >
                <i v-if="disableForm.processing" class="fas fa-spinner animate-spin mr-2"></i>
                {{ disableForm.processing ? 'Disabling...' : 'Disable' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </UserLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import UserLayout from '@/Layouts/UserLayout.vue';

interface Props {
  user: {
    name: string;
    email: string;
    created_at: string;
  };
  twoFactorEnabled: boolean;
  hasRecoveryCodes: boolean;
}

const props = defineProps<Props>();

const activeTab = ref<'personal' | 'password' | 'security'>('personal');
const enableTwoFactorModal = ref(false);
const disableConfirmModal = ref(false);
const regeneratingCodes = ref(false);

const tabs = [
  { id: 'personal', label: 'Personal Info', icon: 'fa-user' },
  { id: 'password', label: 'Password', icon: 'fa-lock' },
  { id: 'security', label: 'Security', icon: 'fa-shield-alt' },
];

const personalForm = useForm({
  name: props.user.name,
  email: props.user.email,
});

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const disableForm = useForm({
  password: '',
});

const submitPersonal = () => {
  personalForm.put(route('user.profile.update'), {
    preserveScroll: true,
  });
};

const submitPassword = () => {
  passwordForm.put(route('user.profile.password'), {
    preserveScroll: true,
    onFinish: () => {
      passwordForm.reset();
    },
  });
};

const regenerateRecoveryCodes = () => {
  regeneratingCodes.value = true;

  useForm({
    password: '',
  }).post(route('user.recovery-codes'), {
    preserveScroll: true,
    onFinish: () => {
      regeneratingCodes.value = false;
    },
  });
};

const disableTwoFactor = () => {
  disableForm.delete(route('user.two-factor.disable'), {
    preserveScroll: true,
    onFinish: () => {
      disableConfirmModal.value = false;
      disableForm.reset();
    },
  });
};
</script>

<style scoped>
/* Page-specific styles */
</style>
