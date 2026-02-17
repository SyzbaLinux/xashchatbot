<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface Props {
  modelValue?: string;
  disabled?: boolean;
}

interface Emits {
  (e: 'update:modelValue', value: string): void;
  (e: 'complete', value: string): void;
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: '',
  disabled: false,
});

const emit = defineEmits<Emits>();

const digits = ref<string[]>(Array(6).fill(''));
const refs = ref<(HTMLInputElement | null)[]>([]);

const code = computed(() => digits.value.join(''));
const isComplete = computed(() => code.value.length === 6 && code.value.every(d => d !== ''));

watch(() => props.modelValue, (newValue) => {
  if (newValue && newValue.length <= 6) {
    digits.value = newValue.split('').concat(Array(6).fill('').slice(newValue.length));
  }
});

watch(code, (newCode) => {
  emit('update:modelValue', newCode);

  if (newCode.length === 6) {
    emit('complete', newCode);
  }
});

const handleInput = (index: number, event: Event) => {
  const input = event.target as HTMLInputElement;
  let value = input.value.replace(/[^0-9]/g, '');

  if (value.length > 1) {
    // Handle paste
    value = value.substring(0, 1);
  }

  digits.value[index] = value;

  if (value && index < 5) {
    setTimeout(() => {
      refs.value[index + 1]?.focus();
    }, 0);
  }
};

const handleKeyDown = (index: number, event: KeyboardEvent) => {
  if (event.key === 'Backspace') {
    event.preventDefault();

    if (digits.value[index]) {
      digits.value[index] = '';
    } else if (index > 0) {
      digits.value[index - 1] = '';
      refs.value[index - 1]?.focus();
    }
  } else if (event.key === 'ArrowLeft' && index > 0) {
    event.preventDefault();
    refs.value[index - 1]?.focus();
  } else if (event.key === 'ArrowRight' && index < 5) {
    event.preventDefault();
    refs.value[index + 1]?.focus();
  }
};

const handlePaste = (event: ClipboardEvent) => {
  event.preventDefault();
  const pastedData = event.clipboardData?.getData('text') || '';
  const pastedDigits = pastedData.replace(/[^0-9]/g, '').substring(0, 6).split('');

  digits.value = pastedDigits.concat(Array(6).fill('').slice(pastedDigits.length));

  if (pastedDigits.length === 6) {
    refs.value[5]?.blur();
  } else if (pastedDigits.length > 0) {
    refs.value[Math.min(pastedDigits.length, 5)]?.focus();
  }
};

const clear = () => {
  digits.value = Array(6).fill('');
  refs.value[0]?.focus();
};

defineExpose({
  clear,
});
</script>

<template>
  <div class="flex gap-3 justify-center">
    <input
      v-for="(digit, index) in digits"
      ref="refs"
      :key="index"
      :value="digit"
      type="text"
      inputmode="numeric"
      maxlength="1"
      :disabled="disabled"
      class="w-14 h-14 text-center text-2xl font-bold rounded-lg border-2 border-emerald-200 focus:border-emerald-500 focus:outline-none transition-colors bg-white dark:bg-slate-800 dark:border-slate-600 dark:focus:border-emerald-400"
      @input="handleInput(index, $event)"
      @keydown="handleKeyDown(index, $event)"
      @paste="handlePaste"
    />
  </div>
</template>
