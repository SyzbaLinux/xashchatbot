<template>
  <GuestLayout>
    <AuthCard title="Sign In" subtitle="Enter your credentials"> 
      <!-- Password Method -->
      <form v-if="authMethod === 'password'" @submit.prevent="submitLogin" class="space-y-4">
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

        <!-- Password field -->
        <FormInput
          v-model="form.password"
          type="password"
          label="Password"
          id="password"
          icon="fa-lock"
          placeholder="Your password"
          required
          autocomplete="current-password"
          :error="form.errors.password"
        />

        <!-- Remember me checkbox -->
        <FormCheckbox
          v-model="form.remember"
          id="remember"
          label="Remember me"
        />

        <!-- Submit button -->
        <AuthButton
          type="submit"
          label="Sign In"
          :loading="form.processing"
          loadingText="Signing in..."
          icon="fa-arrow-right"
        />
      </form>

      <!-- OTP Method -->
      <form v-else @submit.prevent="submitOTP" class="space-y-4">
        <!-- WhatsApp Info -->
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4">
          <p class="text-emerald-700 dark:text-emerald-300 text-sm flex items-start gap-2">
            <i class="fas fa-whatsapp mt-0.5 flex-shrink-0"></i>
            <span>We'll send your login code via WhatsApp. Make sure your phone number is registered with WhatsApp.</span>
          </p>
        </div>

        <!-- Phone Number field -->
        <FormInput
          v-model="otpForm.contact"
          type="tel"
          label="Phone Number"
          id="phone"
          icon="fa-phone"
          placeholder="+1 (555) 000-0000"
          required
          :error="otpForm.errors.contact"
        />

        <!-- Submit button -->
        <AuthButton
          type="submit"
          label="Send WhatsApp OTP"
          :loading="otpForm.processing"
          loadingText="Sending..."
          icon="fa-whatsapp"
        />
      </form> 
    </AuthCard>
  </GuestLayout>
</template>

<script setup lang="ts">
import { defineProps, ref } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthCard from '@/Components/Auth/AuthCard.vue';
import FormInput from '@/Components/Auth/FormInput.vue';
import FormCheckbox from '@/Components/Auth/FormCheckbox.vue';
import AuthButton from '@/Components/Auth/AuthButton.vue';
import { useFlashMessages } from '@/Composables/useFlashMessages';

interface Props {
  canResetPassword: boolean;
  status?: string;
}

const props = defineProps<Props>();
const { showSuccess, showError } = useFlashMessages();

const authMethod = ref<'password' | 'otp'>('password');

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const otpForm = useForm({
  contact: '',
  method: 'whatsapp',
});

const submitLogin = () => {
  form.post(route('login.store'), {
    preserveScroll: true,
    onFinish: () => form.reset('password'),
  });
};

const submitOTP = () => {
  otpForm.post(route('otp.send'), {
    preserveScroll: true,
    onSuccess: () => {
      showSuccess('OTP sent successfully!');
    },
    onError: () => {
      if (otpForm.errors.contact) {
        showError(otpForm.errors.contact);
      } else {
        showError('Failed to send OTP. Please try again.');
      }
    },
  });
};
</script>

<style scoped>
/* Component-specific styles */
</style>
