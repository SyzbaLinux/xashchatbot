<template>
  <GuestLayout>
    <AuthCard title="Create Account" subtitle="Get started in seconds">
      <form @submit.prevent="submitRegister" class="space-y-4">
        <!-- Name field -->
        <FormInput
          v-model="form.name"
          type="text"
          label="Full Name"
          id="name"
          icon="fa-user"
          placeholder="John Doe"
          required
          autocomplete="name"
          :error="form.errors.name"
        />

        <!-- Email field -->
        <FormInput
          v-model="form.email"
          type="email"
          label="Email"
          id="email"
          icon="fa-envelope"
          placeholder="you@example.com"
          required
          autocomplete="email"
          :error="form.errors.email"
        />

        <!-- Password fields side by side -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
          <!-- Password field -->
          <div>
            <FormInput
              v-model="form.password"
              type="password"
              label="Password"
              id="password"
              icon="fa-lock"
              placeholder="Create strong password"
              required
              autocomplete="new-password"
              :error="form.errors.password"
            />
            <PasswordStrengthIndicator :password="form.password" />
          </div>

          <!-- Confirm password field -->
          <div>
            <FormInput
              v-model="form.password_confirmation"
              type="password"
              label="Confirm"
              id="password_confirmation"
              icon="fa-lock"
              placeholder="Re-enter password"
              required
              autocomplete="new-password"
              :error="form.errors.password_confirmation"
            />
          </div>
        </div>

        <!-- Submit button -->
        <AuthButton
          type="submit"
          label="Create Account"
          :loading="form.processing"
          loadingText="Creating..."
          icon="fa-user-plus"
        />
      </form>

      <!-- Links -->
      <div class="mt-5 text-center text-sm text-gray-600 dark:text-gray-400">
        Already have an account?
        <Link
          :href="route('login')"
          class="font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors"
        >
          Sign in
        </Link>
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
import PasswordStrengthIndicator from '@/Components/Auth/PasswordStrengthIndicator.vue';

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submitRegister = () => {
  form.post(route('register.store'), {
    preserveScroll: true,
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<style scoped>
/* Component-specific styles */
</style>
