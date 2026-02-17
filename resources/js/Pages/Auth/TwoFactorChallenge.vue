<template>
  <GuestLayout>
    <AuthCard
      title="Two-Factor Authentication"
      :subtitle="recovery ? 'Use your recovery code' : 'Enter your 6-digit code'"
    >
      <form @submit.prevent="submitTwoFactor" class="space-y-4">
        <!-- Info message -->
        <div class="p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
          <p class="text-sm text-blue-700 dark:text-blue-300 flex items-start gap-2">
            <i class="fas fa-shield-halved mt-0.5 flex-shrink-0"></i>
            <span>
              <span v-if="!recovery">Your account is protected with two-factor authentication.</span>
              <span v-else>Use one of your recovery codes to sign in if you don't have your authenticator app.</span>
            </span>
          </p>
        </div>

        <!-- Code/Recovery code input -->
        <div>
          <FormInput
            v-if="!recovery"
            v-model="form.code"
            type="text"
            label="Authentication Code"
            id="code"
            icon="fa-key"
            placeholder="000000"
            maxlength="6"
            inputmode="numeric"
            required
            autocomplete="off"
            :error="form.errors.code"
            hint="Enter the 6-digit code from your authenticator app"
          />
          <FormInput
            v-else
            v-model="form.recovery_code"
            type="text"
            label="Recovery Code"
            id="recovery_code"
            icon="fa-shield-check"
            placeholder="XXXX-XXXX-XXXX"
            required
            autocomplete="off"
            :error="form.errors.recovery_code"
            hint="Enter one of your recovery codes"
          />
        </div>

        <!-- Submit button -->
        <AuthButton
          type="submit"
          label="Verify"
          :loading="form.processing"
          loadingText="Verifying..."
          icon="fa-circle-check"
        />
      </form>

      <!-- Toggle recovery mode -->
      <div class="mt-4 text-center text-sm">
        <button
          type="button"
          @click="recovery = !recovery"
          class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium transition-colors"
        >
          <i :class="`fas ${recovery ? 'fa-key' : 'fa-shield-check'} mr-1`"></i>
          {{ recovery ? 'Use code instead' : 'Use recovery code' }}
        </button>
      </div>

      <!-- Back to login -->
      <div class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
        <Link
          :href="route('login')"
          class="font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors"
        >
          Sign In
        </Link>
      </div>
    </AuthCard>
  </GuestLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthCard from '@/Components/Auth/AuthCard.vue';
import FormInput from '@/Components/Auth/FormInput.vue';
import AuthButton from '@/Components/Auth/AuthButton.vue';

const recovery = ref(false);

const form = useForm({
  code: '',
  recovery_code: '',
});

const submitTwoFactor = () => {
  form.post(route('two-factor.login.store'), {
    preserveScroll: true,
    onFinish: () => {
      form.reset('code', 'recovery_code');
    },
  });
};
</script>

<style scoped>
/* Component-specific styles */
</style>
