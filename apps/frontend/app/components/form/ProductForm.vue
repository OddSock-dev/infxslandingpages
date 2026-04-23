<script setup lang="ts">
import { useJourney } from '~/composables/useJourney'
import { usePrefill } from '~/composables/usePrefill'
import { useSubmit } from '~/composables/useSubmit'

const props = defineProps<{ productKey: string }>()
const { initFromToken } = useJourney()
const { prefillFields } = usePrefill()
const { submit, isLoading, error } = useSubmit()

const name = ref('')
const email = ref('')
const phone = ref('')
const company = ref('')
const validationError = ref<string | null>(null)

watch(prefillFields, (fields) => {
  if (!fields) return
  if (fields.name) name.value = fields.name
  if (fields.email) email.value = fields.email
  if (fields.phone) phone.value = fields.phone
  if (fields.company) company.value = fields.company
}, { immediate: true })

onMounted(async () => { await initFromToken() })

const displayTitle = computed(() =>
  props.productKey.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase())
)

async function handleSubmit() {
  if (!name.value.trim() || !email.value.trim()) {
    validationError.value = 'Full name and email are required.'
    return
  }
  validationError.value = null
  await submit({
    product_key: props.productKey,
    name: name.value.trim(),
    email: email.value.trim(),
    phone: phone.value.trim() || undefined,
    company: company.value.trim() || undefined,
  })
}
</script>

<template>
  <div class="card shadow-xl ring-1 ring-slate-900/5 overflow-hidden">
    <!-- Card header strip -->
    <div class="bg-brand-600 px-8 py-5">
      <p class="text-xs font-semibold uppercase tracking-widest text-indigo-200 mb-0.5">Free Consultation</p>
      <h2 class="text-lg font-bold text-white">Get started with {{ displayTitle }}</h2>
      <p class="text-sm text-indigo-200 mt-0.5">Our team will reach out within one business day.</p>
    </div>
    <div class="p-8">
      <form class="space-y-4" @submit.prevent="handleSubmit">
        <FormField v-model="name" label="Full Name" name="name" type="text" placeholder="Jane Smith" :required="true" />
        <FormField v-model="email" label="Work Email" name="email" type="email" placeholder="jane@company.co.za" :required="true" />
        <FormField v-model="phone" label="Phone Number" name="phone" type="tel" placeholder="+27 82 123 4567" />
        <FormField v-model="company" label="Company Name" name="company" type="text" placeholder="Acme (Pty) Ltd" />
        <p v-if="validationError" class="text-xs text-red-500">{{ validationError }}</p>
        <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
        <button
          type="submit" :disabled="isLoading"
          class="w-full rounded-xl bg-accent-500 px-6 py-3.5 text-sm font-semibold text-white btn-accent hover:bg-accent-600 disabled:opacity-60 disabled:cursor-not-allowed"
        >
          {{ isLoading ? 'Submitting…' : 'Request Free Consultation →' }}
        </button>
      </form>
      <p class="mt-4 text-center text-xs text-slate-400">POPIA compliant · No spam · Cancel anytime</p>
    </div>
  </div>
</template>
