<template>
  <div v-if="password" class="mt-3 mb-4">
    <!-- Strength bar -->
    <div class="flex gap-1 mb-2">
      <div
        v-for="i in 4"
        :key="i"
        :class="[
          'h-2 rounded-full flex-1 transition-all duration-300',
          i <= strength
            ? strengthColors[strength]
            : 'bg-gray-300 dark:bg-gray-600'
        ]"
      ></div>
    </div>

    <!-- Strength label and score -->
    <div class="flex items-center justify-between">
      <span class="text-xs font-semibold" :class="strengthLabelClasses">
        <i :class="`fas ${strengthIcon} mr-1`"></i>
        {{ strengthLabel }}
      </span>
      <span class="text-xs text-gray-500 dark:text-gray-400">
        {{ Math.round((strength / 4) * 100) }}%
      </span>
    </div>

    <!-- Requirements checklist -->
    <div class="mt-3 space-y-1.5 text-xs">
      <div class="flex items-center" :class="hasLength ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
        <i :class="`fas ${hasLength ? 'fa-check-circle' : 'fa-circle'} mr-1.5`"></i>
        At least 8 characters
      </div>
      <div class="flex items-center" :class="hasUpperLower ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
        <i :class="`fas ${hasUpperLower ? 'fa-check-circle' : 'fa-circle'} mr-1.5`"></i>
        Uppercase and lowercase letters
      </div>
      <div class="flex items-center" :class="hasNumber ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
        <i :class="`fas ${hasNumber ? 'fa-check-circle' : 'fa-circle'} mr-1.5`"></i>
        At least one number
      </div>
      <div class="flex items-center" :class="hasSpecial ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400'">
        <i :class="`fas ${hasSpecial ? 'fa-check-circle' : 'fa-circle'} mr-1.5`"></i>
        Special character (!@#$%^&*)
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
  password: string;
}>();

// Check individual requirements
const hasLength = computed(() => props.password.length >= 8);
const hasUpperLower = computed(() => /[a-z]/.test(props.password) && /[A-Z]/.test(props.password));
const hasNumber = computed(() => /\d/.test(props.password));
const hasSpecial = computed(() => /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(props.password));

// Calculate strength (0-4)
const strength = computed(() => {
  let score = 0;
  if (hasLength.value) score++;
  if (hasUpperLower.value) score++;
  if (hasNumber.value) score++;
  if (hasSpecial.value) score++;
  return score;
});

// Strength label and visual feedback
const strengthLabel = computed(() => {
  if (strength.value === 0) return 'No password';
  if (strength.value === 1) return 'Weak';
  if (strength.value === 2) return 'Fair';
  if (strength.value === 3) return 'Good';
  return 'Strong';
});

const strengthLabelClasses = computed(() => {
  if (strength.value === 0) return 'text-gray-500 dark:text-gray-400';
  if (strength.value === 1) return 'text-red-600 dark:text-red-400';
  if (strength.value === 2) return 'text-yellow-600 dark:text-yellow-400';
  if (strength.value === 3) return 'text-blue-600 dark:text-blue-400';
  return 'text-green-600 dark:text-green-400';
});

const strengthIcon = computed(() => {
  if (strength.value === 0) return 'fa-circle-question';
  if (strength.value === 1) return 'fa-circle-exclamation';
  if (strength.value === 2) return 'fa-triangle-exclamation';
  if (strength.value === 3) return 'fa-check-circle';
  return 'fa-shield-check';
});

const strengthColors = {
  1: 'bg-red-500',
  2: 'bg-yellow-500',
  3: 'bg-blue-500',
  4: 'bg-green-500',
};
</script>

<style scoped>
/* Animation utilities */
</style>
