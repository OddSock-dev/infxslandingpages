<script setup lang="ts">
defineProps<{
  items: Array<{ question: string; answer: string }>
}>()
</script>

<template>
  <section class="bg-white py-24">
    <div class="mx-auto max-w-3xl px-6 lg:px-8">
      <div class="mb-12">
        <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
          Frequently asked questions
        </h2>
      </div>
      <div class="divide-y divide-slate-100">
        <details
          v-for="item in items"
          :key="item.question"
          class="group py-5"
        >
          <summary class="flex cursor-pointer items-start justify-between gap-4 list-none">
            <span class="text-base font-medium text-slate-900 group-open:text-brand-600 transition-colors">
              {{ item.question }}
            </span>
            <svg
              class="mt-0.5 h-5 w-5 shrink-0 text-slate-400 transition-transform duration-200 group-open:rotate-180"
              fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
          </summary>
          <p class="mt-4 text-sm text-slate-500 leading-relaxed pr-12">{{ item.answer }}</p>
        </details>
      </div>
    </div>
  </section>
</template>
