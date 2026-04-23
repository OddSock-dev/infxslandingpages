<template>
  <section class="border-y border-slate-100 bg-white py-6">
    <div class="mx-auto max-w-6xl px-6 lg:px-8">
      <p class="mb-4 text-center text-[11px] font-semibold uppercase tracking-widest text-slate-400">
        Certifications &amp; Credentials
      </p>
      <div class="flex flex-wrap items-center justify-center gap-3">
        <div
          v-for="badge in badges"
          :key="badge.label"
          class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 shadow-sm"
        >
          <svg
            class="h-3.5 w-3.5 shrink-0 text-brand-600"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
            aria-hidden="true"
            v-html="badge.icon"
          />
          <span class="text-xs font-semibold text-slate-700">{{ badge.label }}</span>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const badges = [
  {
    label: 'Zoho Authorised Partner',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-.702 3.642 3.745 3.745 0 01-3.642.702A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 01-3.642-.702 3.745 3.745 0 01-.702-3.642A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 01.702-3.642 3.745 3.745 0 013.642-.702A3.745 3.745 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 013.642.702 3.745 3.745 0 01.702 3.642A3.745 3.745 0 0121 12z" />',
  },
  {
    label: 'SA-Based Support',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />',
  },
  {
    label: 'ISO 27001 / POPIA',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />',
  },
  {
    label: 'Fast Implementation',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />',
  },
  {
    label: '5-Star Rated',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />',
  },
]
</script>
