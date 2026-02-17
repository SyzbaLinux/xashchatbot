<template>
  <GuestLayout>
    <AuthCard
      title="Reset Password"
      subtitle="Create your new password"
    >
      <form @submit.prevent="submitResetPassword" class="space-y-4">
        <!-- Email field (read-only) -->
        <FormInput
          :modelValue="form.email"
          type="email"
          label="Email Address"
          id="email"
          icon="fa-envelope"
          required
          disabled
          @update:modelValue="(val) => form.email = val"
        />

        <!-- Password field -->
        <div>
          <FormInput
            v-model="form.password"
            type="password"
            label="New Password"
            id="password"
            icon="fa-lock"
            placeholder="Create a strong password"
            required
            autocomplete="new-password"
            :error="form.errors.password"
          />

          <!-- Password strength indicator -->
          <PasswordStrengthIndicator :password="form.password" />
        </div>

        <!-- Confirm password field -->
        <FormInput
          v-model="form.password_confirmation"
          type="password"
          label="Confirm Password"
          id="password_confirmation"
          icon="fa-lock-check"
          placeholder="Re-enter your password"
          required
          autocomplete="new-password"
          :error="form.errors.password_confirmation"
        />

        <!-- Submit button -->
        <AuthButton
          type="submit"
          label="Reset Password"
          :loading="form.processing"
          loadingText="Resetting..."
          icon="fa-refresh"
        />
      </form>

      <!-- Back to login link -->
      <div class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
        <Link
          :href="route('login')"
          class="font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors"
        >
          Back to Sign In
        </Link>
      </div>
    </AuthCard>
  </GuestLayout>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthCard from '@/Components/Auth/AuthCard.vue';
import FormInput from '@/Components/Auth/FormInput.vue';
import AuthButton from '@/Components/Auth/AuthButton.vue';
import PasswordStrengthIndicator from '@/Components/Auth/PasswordStrengthIndicator.vue';

interface Props {
  email: string;
  token: string;
}

const props = defineProps<Props>();

const form = useForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

const submitResetPassword = () => {
  form.post(route('password.update'), {
    preserveScroll: true,
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<style scoped>
/* Component-specific styles */
</style>
