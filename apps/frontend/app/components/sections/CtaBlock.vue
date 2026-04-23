<script setup lang="ts">
defineProps<{
  ctaText: string
  ctaUrl: string
  trialUrl?: string
  headline?: string
  subheadline?: string
}>()
</script>

<template>
  <section class="bg-brand-600 py-24">
    <div class="mx-auto max-w-6xl px-6 lg:px-8">
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-10">
        <div class="max-w-2xl">
          <h2 class="text-3xl font-bold text-white sm:text-4xl">
            {{ headline ?? 'Ready to transform your business with Zoho?' }}
          </h2>
          <p class="mt-4 text-lg text-indigo-200">
            {{ subheadline ?? 'Book a free 15-minute discovery call with a certified Zoho expert. No obligation, no pressure.' }}
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 shrink-0">
          <!-- Use <a> not NuxtLink — ctaUrl is often a hash anchor (/#qualify) -->
          <a
            :href="ctaUrl"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-7 py-3.5 text-base font-semibold text-brand-600 hover:bg-indigo-50 transition-colors"
          >
            {{ ctaText }}
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
          </a>
          <a
            v-if="trialUrl"
            :href="trialUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center justify-center gap-2 rounded-xl border border-indigo-400 px-7 py-3.5 text-base font-semibold text-white hover:bg-indigo-700 transition-colors"
          >
            Start Free Trial ↗
          </a>
        </div>
      </div>
      <p class="mt-8 text-sm text-indigo-300">No credit card required · 15-minute call · POPIA compliant</p>
    </div>
  </section>
</template>
