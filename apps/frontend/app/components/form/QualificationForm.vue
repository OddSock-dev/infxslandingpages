<script setup lang="ts">
import { useQualify } from '~/composables/useQualify'
import type { QualifyRequest } from '~/types'

const { qualify, isLoading, error } = useQualify()

const currentStep = ref(0)
const selectedProduct = ref('')
const consentGiven = ref(false)
const validationError = ref<string | null>(null)

const productOptions = [
  { value: 'zoho_one', label: 'Zoho One — All-in-one business suite' },
  { value: 'zoho_marketing_plus', label: 'Zoho Marketing Plus — Unified marketing platform' },
  { value: 'zoho_workplace', label: 'Zoho Workplace — Collaboration suite' },
]

function handleNext() {
  if (!selectedProduct.value) {
    validationError.value = 'Please select a product to continue.'
    return
  }
  validationError.value = null
  currentStep.value++
}

function handleBack() {
  currentStep.value--
  validationError.value = null
}

async function handleSubmit() {
  if (!consentGiven.value) {
    validationError.value = 'Please confirm you agree to be contacted.'
    return
  }
  validationError.value = null
  const payload: QualifyRequest = {
    source_page_key: 'landing',
    answers: [{ step_key: 'step_1', field_key: 'product_interest', value: selectedProduct.value }],
    consent: true,
  }
  await qualify(payload)
}
</script>

<template>
  <div class="card p-8 shadow-xl ring-1 ring-slate-900/5">
    <!-- Header -->
    <div class="mb-8 pb-6 border-b border-slate-100">
      <p class="text-xs font-semibold uppercase tracking-widest text-brand-600 mb-1.5">Free · No Obligation</p>
      <h2 class="text-xl font-bold text-slate-900">Find Your Perfect Zoho Solution</h2>
      <p class="mt-1 text-sm text-slate-500">Answer two quick questions — matched instantly.</p>
    </div>

    <MultiStepForm
      :steps="2"
      :current-step="currentStep"
      :is-loading="isLoading"
      @next="handleNext"
      @back="handleBack"
      @submit="handleSubmit"
    >
      <FormStep
        v-if="currentStep === 0"
        title="What are you looking for?"
        description="Select the Zoho product that best fits your needs."
      >
        <div class="mt-4 space-y-2.5">
          <label
            v-for="opt in productOptions"
            :key="opt.value"
            class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition-all"
            :class="selectedProduct === opt.value
              ? 'border-brand-500 bg-brand-50 ring-1 ring-brand-500'
              : 'border-slate-200 bg-white hover:border-brand-300'"
          >
            <input
              v-model="selectedProduct" type="radio" :value="opt.value"
              class="h-4 w-4 border-slate-300 text-brand-600 focus:ring-brand-500"
            />
            <span class="text-sm font-medium" :class="selectedProduct === opt.value ? 'text-brand-700' : 'text-slate-700'">
              {{ opt.label }}
            </span>
          </label>
        </div>
        <p v-if="validationError" class="mt-3 text-xs text-red-500">{{ validationError }}</p>
      </FormStep>

      <FormStep
        v-if="currentStep === 1"
        title="One last step"
        description="Confirm and we'll match you instantly."
      >
        <label class="mt-4 flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white p-4 hover:border-brand-300 transition-all">
          <input
            v-model="consentGiven" type="checkbox"
            class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 accent-brand-600"
          />
          <span class="text-sm text-slate-600 leading-relaxed">
            I agree to be contacted by INFX Solutions about Zoho products and services that match my needs.
          </span>
        </label>
        <p v-if="validationError" class="mt-2 text-xs text-red-500">{{ validationError }}</p>
        <p v-if="error" class="mt-2 text-xs text-red-500">{{ error }}</p>
        <p class="mt-4 text-xs text-slate-400">Your data is encrypted and POPIA compliant. No spam, ever.</p>
      </FormStep>
    </MultiStepForm>
  </div>
</template>
