<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthCard from '@/Components/Auth/AuthCard.vue';
import AuthButton from '@/Components/Auth/AuthButton.vue';
import OTPCodeInput from '@/Components/Auth/OTPCodeInput.vue';
import { useFlashMessages } from '@/Composables/useFlashMessages';

interface Props {
  phoneNumber?: string | null;
  email?: string | null;
  method: string;
  contact: string;
}

const props = defineProps<Props>();

const { showSuccess, showError } = useFlashMessages();

const form = useForm({
  code: '',
  contact: props.contact,
});

const otpCodeRef = ref<InstanceType<typeof OTPCodeInput>>();
const timeRemaining = ref(600); // 10 minutes in seconds
const isResending = ref(false);
const selectedMethod = ref<'whatsapp' | 'sms' | 'email'>(props.method === 'email' ? 'email' : props.method === 'sms' ? 'sms' : 'whatsapp');

const methods = [
  {
    id: 'whatsapp',
    name: 'WhatsApp',
    icon: 'fab fa-whatsapp',
    description: 'Receive OTP via WhatsApp',
  },
  {
    id: 'sms',
    name: 'SMS',
    icon: 'fas fa-sms',
    description: 'Receive OTP via SMS',
  },
  {
    id: 'email',
    name: 'Email',
    icon: 'fas fa-envelope',
    description: 'Receive OTP via Email',
  },
];

const displayContact = computed(() => {
  if (props.phoneNumber) return props.phoneNumber;
  if (props.email) return props.email;
  return props.contact;
});

const formattedTime = computed(() => {
  const minutes = Math.floor(timeRemaining.value / 60);
  const seconds = timeRemaining.value % 60;
  return `${minutes}:${seconds.toString().padStart(2, '0')}`;
});

const canResend = computed(() => timeRemaining.value === 0);

onMounted(() => {
  // Start countdown timer
  const timer = setInterval(() => {
    if (timeRemaining.value > 0) {
      timeRemaining.value--;
    } else {
      clearInterval(timer);
    }
  }, 1000);
});

const handleCodeComplete = (code: string) => {
  form.code = code;
  submitForm();
};

const submitForm = () => {
  form.post(route('otp.verify'), {
    preserveScroll: true,
    onSuccess: () => {
      showSuccess('OTP verified successfully! Logging you in...');
    },
    onError: () => {
      otpCodeRef.value?.clear();
      if (form.errors.code) {
        showError(form.errors.code);
      } else {
        showError('Verification failed. Please try again.');
      }
    },
  });
};

const resendOTP = async () => {
  isResending.value = true;

  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch(route('otp.resend'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token || '',
      },
      body: JSON.stringify({
        method: selectedMethod.value,
        contact: props.contact,
      }),
    });

    const data = await response.json();

    if (data.success) {
      showSuccess(`OTP sent via ${selectedMethod.value === 'email' ? 'Email' : selectedMethod.value.toUpperCase()}!`);
      timeRemaining.value = data.data?.expires_in_seconds || 600;
      otpCodeRef.value?.clear();
      form.reset('code');
    } else {
      showError(data.message || 'Failed to resend OTP');
    }
  } catch (error) {
    showError('Failed to resend OTP. Please try again.');
  } finally {
    isResending.value = false;
  }
};

const switchMethod = async (newMethod: string) => {
  if (newMethod === selectedMethod.value) return;

  selectedMethod.value = newMethod as typeof selectedMethod.value;
  isResending.value = true;

  try {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch(route('otp.send'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token || '',
      },
      body: JSON.stringify({
        method: newMethod,
        contact: props.contact,
      }),
    });

    const data = await response.json();

    if (data.success) {
      showSuccess(`OTP sent via ${newMethod === 'email' ? 'Email' : newMethod.toUpperCase()}!`);
      timeRemaining.value = data.data?.expires_in_seconds || 600;
      otpCodeRef.value?.clear();
      form.reset('code');
    } else {
      selectedMethod.value = props.method as typeof selectedMethod.value;
      showError(data.message || `Failed to send OTP via ${newMethod}`);
    }
  } catch (error) {
    selectedMethod.value = props.method as typeof selectedMethod.value;
    showError('Failed to switch method. Please try again.');
  } finally {
    isResending.value = false;
  }
};
</script>

<template>
  <GuestLayout>
    <AuthCard title="Verify Your Identity" subtitle="Enter the verification code we sent">
      <div class="space-y-6">
        <!-- Contact Display -->
        <div class="text-center">
          <p class="text-sm text-slate-600 dark:text-slate-400">
            Verification code sent to
          </p>
          <p class="text-lg font-semibold text-slate-900 dark:text-white mt-1">
            {{ displayContact }}
          </p>
        </div>

        <!-- Method Selection Tabs -->
        <div class="flex gap-2">
          <button
            v-for="methodOption in methods"
            :key="methodOption.id"
            @click="switchMethod(methodOption.id)"
            :disabled="isResending"
            class="flex-1 py-3 px-3 rounded-lg border-2 transition-colors text-center text-sm font-medium"
            :class="{
              'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400': selectedMethod === methodOption.id,
              'border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:border-slate-300 dark:hover:border-slate-500': selectedMethod !== methodOption.id,
              'opacity-50 cursor-not-allowed': isResending,
            }"
          >
            <i :class="methodOption.icon" class="mr-1"></i>
            {{ methodOption.name }}
          </button>
        </div>

        <!-- OTP Code Input -->
        <div class="space-y-4">
          <p class="text-center text-sm text-slate-600 dark:text-slate-400">
            Enter the 6-digit code below
          </p>
          <OTPCodeInput
            ref="otpCodeRef"
            v-model="form.code"
            :disabled="form.processing"
            @complete="handleCodeComplete"
          />

          <!-- Error Message -->
          <div v-if="form.errors.code" class="text-center text-sm text-red-600 dark:text-red-400">
            <i class="fas fa-exclamation-circle mr-1"></i>
            {{ form.errors.code }}
          </div>
        </div>

        <!-- Submit Button -->
        <AuthButton
          @click="submitForm"
          :disabled="form.code.length !== 6 || form.processing"
          class="w-full"
        >
          <span v-if="!form.processing">Verify Code</span>
          <span v-else><i class="fas fa-spinner fa-spin mr-2"></i>Verifying...</span>
        </AuthButton>

        <!-- Resend Code -->
        <div class="text-center">
          <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">
            Didn't receive the code?
          </p>
          <button
            v-if="!canResend"
            disabled
            class="text-sm font-medium text-slate-400 dark:text-slate-500"
          >
            Resend in <span class="font-bold">{{ formattedTime }}</span>
          </button>
          <button
            v-else
            @click="resendOTP"
            :disabled="isResending"
            class="text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="!isResending">Resend Code</span>
            <span v-else><i class="fas fa-spinner fa-spin mr-1"></i>Sending...</span>
          </button>
        </div>

        <!-- Security Notice -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
          <p class="text-sm text-blue-800 dark:text-blue-300">
            <i class="fas fa-lock mr-2"></i>
            Never share this code. We'll never ask you for it via email or phone.
          </p>
        </div>

        <!-- Back to Login -->
        <div class="text-center">
          <p class="text-sm text-slate-600 dark:text-slate-400">
            <a href="/login" class="font-medium text-emerald-600 dark:text-emerald-400 hover:underline">
              Back to login
            </a>
          </p>
        </div>
      </div>
    </AuthCard>
  </GuestLayout>
</template>
