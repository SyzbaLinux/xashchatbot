<template>
  <GuestLayout>
    <AuthCard
      title="Verify Email"
      subtitle="Check your inbox for a verification link"
    >
      <!-- Success message -->
      <div v-if="status" class="p-4 mb-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-lg">
        <p class="text-sm text-emerald-700 dark:text-emerald-300 flex items-start gap-2">
          <i class="fas fa-check-circle mt-0.5 flex-shrink-0"></i>
          <span>{{ status }}</span>
        </p>
      </div>

      <!-- Instructions -->
      <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <p class="text-sm text-blue-700 dark:text-blue-300 mb-3 flex items-start gap-2">
          <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
          <span>A fresh verification link has been sent to your email address.</span>
        </p>
        <p class="text-sm text-blue-700 dark:text-blue-300 flex items-start gap-2">
          <i class="fas fa-lightbulb mt-0.5 flex-shrink-0"></i>
          <span>Before proceeding, please check your email for a verification link. If you didn't receive the email, we will gladly send you another.</span>
        </p>
      </div>

      <!-- Resend button -->
      <form @submit.prevent="submitResend" class="mb-4">
        <AuthButton
          type="submit"
          label="Resend Email"
          :loading="resendForm.processing"
          loadingText="Sending..."
          icon="fa-paper-plane"
        />
      </form>

      <!-- Logout -->
      <form @submit.prevent="logout">
        <AuthButton
          type="submit"
          label="Sign Out"
          variant="secondary"
          icon="fa-sign-out-alt"
        />
      </form>
    </AuthCard>
  </GuestLayout>
</template>

<script setup lang="ts">
import { defineProps } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthCard from '@/Components/Auth/AuthCard.vue';
import AuthButton from '@/Components/Auth/AuthButton.vue';

interface Props {
  status?: string;
}

defineProps<Props>();

const resendForm = useForm({});

const submitResend = () => {
  resendForm.post(route('verification.send'), {
    preserveScroll: true,
  });
};

const logout = () => {
  router.post(route('logout'));
};
</script>

<style scoped>
/* Component-specific styles */
</style>
