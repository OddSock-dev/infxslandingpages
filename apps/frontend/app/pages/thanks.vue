<script setup lang="ts">
import { useSeo } from '~/composables/useSeo'
definePageMeta({ layout: 'minimal' })
useSeo({ title: 'Thank You | INFX Solutions', description: 'We\'ve received your enquiry.', robots: 'noindex, nofollow' })
</script>

<template>
  <div class="max-w-md w-full mx-auto px-6 py-16 text-center">
    <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100">
      <svg class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
      </svg>
    </div>
    <h1 class="text-3xl font-bold text-slate-900">You're all set</h1>
    <p class="mt-4 text-slate-500 leading-relaxed">
      We've received your enquiry. A Zoho specialist will be in touch within <strong class="text-slate-900 font-semibold">one business day</strong>.
    </p>
    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
      <NuxtLink to="/" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-6 py-3 text-sm font-semibold text-white btn-primary hover:bg-brand-700">
        ← Return Home
      </NuxtLink>
      <a href="https://www.zoho.com/blog" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
        Zoho Blog ↗
      </a>
    </div>
  </div>
</template>
