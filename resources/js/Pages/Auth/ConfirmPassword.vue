<template>
  <GuestLayout>
    <AuthCard
      title="Confirm Password"
      subtitle="Verify your identity"
    >
      <form @submit.prevent="submitConfirmPassword" class="space-y-4">
        <!-- Message -->
        <div class="p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
          <p class="text-sm text-blue-700 dark:text-blue-300 flex items-start gap-2">
            <i class="fas fa-shield-alt mt-0.5 flex-shrink-0"></i>
            <span>For your security, please confirm your password before continuing.</span>
          </p>
        </div>

        <!-- Password field -->
        <FormInput
          v-model="form.password"
          type="password"
          label="Password"
          id="password"
          icon="fa-lock"
          placeholder="Enter your password"
          required
          autocomplete="current-password"
          :error="form.errors.password"
        />

        <!-- Submit button -->
        <AuthButton
          type="submit"
          label="Confirm Password"
          :loading="form.processing"
          loadingText="Confirming..."
          icon="fa-check"
        />
      </form>

      <!-- Info -->
      <div class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
        <p><Link :href="route('login')" class="font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors">Sign out</Link></p>
      </div>
    </AuthCard>
  </GuestLayout>
</template>

<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthCard from '@/Components/Auth/AuthCard.vue';
import FormInput from '@/Components/Auth/FormInput.vue';
import AuthButton from '@/Components/Auth/AuthButton.vue';

const form = useForm({
  password: '',
});

const submitConfirmPassword = () => {
  form.post(route('password.confirm.store'), {
    preserveScroll: true,
    onFinish: () => form.reset(),
  });
};
</script>

<style scoped>
/* Component-specific styles */
</style>
