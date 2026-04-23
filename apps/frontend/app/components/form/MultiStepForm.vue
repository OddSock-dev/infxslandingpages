<script setup lang="ts">
const props = defineProps<{ steps: number; currentStep: number; isLoading?: boolean }>()
const emit = defineEmits<{ next: []; back: []; submit: [] }>()
const progress = computed(() => Math.round(((props.currentStep + 1) / props.steps) * 100))
const isFirst = computed(() => props.currentStep === 0)
const isLast = computed(() => props.currentStep === props.steps - 1)
</script>

<template>
  <div>
    <!-- Progress bar -->
    <div class="mb-8">
      <div class="flex items-center justify-between mb-2.5">
        <span class="text-xs font-medium text-slate-500">Step {{ currentStep + 1 }} of {{ steps }}</span>
        <span class="text-xs font-semibold text-brand-600">{{ progress }}%</span>
      </div>
      <div class="h-1.5 w-full rounded-full bg-slate-100">
        <div
          class="h-1.5 rounded-full bg-brand-600 transition-all duration-500"
          :style="{ width: `${progress}%` }"
        />
      </div>
    </div>

    <slot />

    <div class="mt-8 flex justify-between gap-3">
      <button
        v-if="!isFirst"
        type="button"
        class="rounded-lg border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all"
        @click="emit('back')"
      >
        ← Back
      </button>
      <div v-else />
      <button
        v-if="!isLast"
        type="button"
        class="rounded-lg bg-brand-600 px-6 py-2.5 text-sm font-semibold text-white btn-primary hover:bg-brand-700"
        @click="emit('next')"
      >
        Continue →
      </button>
      <button
        v-else
        type="button"
        :disabled="isLoading"
        class="rounded-lg bg-accent-500 px-6 py-2.5 text-sm font-semibold text-white btn-accent hover:bg-accent-600 disabled:opacity-60 disabled:cursor-not-allowed"
        @click="emit('submit')"
      >
        {{ isLoading ? 'Matching you now…' : 'Find My Solution →' }}
      </button>
    </div>
  </div>
</template>
