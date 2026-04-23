<script setup lang="ts">
const props = defineProps<{
  modelValue: string | boolean
  label: string
  type: 'text' | 'email' | 'tel' | 'select' | 'radio' | 'checkbox'
  name: string
  placeholder?: string
  required?: boolean
  options?: Array<{ value: string; label: string }>
  error?: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string | boolean]
}>()
</script>

<template>
  <div class="space-y-1.5">
    <label v-if="type !== 'checkbox'" :for="name" class="block text-sm font-medium text-slate-700">
      {{ label }}
      <span v-if="required" class="text-brand-600 ml-0.5">*</span>
    </label>

    <!-- Text / Email / Tel -->
    <input
      v-if="['text', 'email', 'tel'].includes(type)"
      :id="name" :name="name" :type="type"
      :placeholder="placeholder" :required="required"
      :value="modelValue as string"
      class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-xs transition-all focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
      @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
    />

    <!-- Select -->
    <select
      v-else-if="type === 'select'"
      :id="name" :name="name" :required="required"
      :value="modelValue as string"
      class="w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-xs transition-all focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"
      @change="emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
    >
      <option value="" disabled>Select an option</option>
      <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
    </select>

    <!-- Radio group -->
    <div v-else-if="type === 'radio'" class="space-y-2">
      <label
        v-for="opt in options" :key="opt.value"
        class="flex cursor-pointer items-center gap-3 rounded-lg border p-3.5 transition-all"
        :class="modelValue === opt.value
          ? 'border-brand-500 bg-brand-50 ring-1 ring-brand-500'
          : 'border-slate-200 bg-white hover:border-brand-300 hover:bg-brand-50/40'"
      >
        <input
          :name="name" type="radio" :value="opt.value" :checked="modelValue === opt.value"
          class="h-4 w-4 border-slate-300 text-brand-600 focus:ring-brand-500"
          @change="emit('update:modelValue', opt.value)"
        />
        <span class="text-sm font-medium" :class="modelValue === opt.value ? 'text-brand-700' : 'text-slate-700'">
          {{ opt.label }}
        </span>
      </label>
    </div>

    <!-- Checkbox -->
    <label
      v-else-if="type === 'checkbox'"
      class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-white p-4 hover:border-brand-300 transition-all"
    >
      <input
        :name="name" type="checkbox" :checked="modelValue as boolean"
        class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
        @change="emit('update:modelValue', ($event.target as HTMLInputElement).checked)"
      />
      <span class="text-sm text-slate-600">{{ label }}</span>
    </label>

    <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
  </div>
</template>
