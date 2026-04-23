<template>
  <nav class="hidden md:flex items-center gap-0.5">
    <NuxtLink
      to="/"
      class="rounded-md px-3.5 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all"
    >
      Home
    </NuxtLink>

    <div class="relative group">
      <button class="flex items-center gap-1 rounded-md px-3.5 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all">
        Products
        <svg class="h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-180 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <div class="absolute top-full left-1/2 -translate-x-1/2 pt-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150">
        <div class="w-64 rounded-xl bg-white shadow-lg ring-1 ring-slate-900/5 p-1.5">
          <NuxtLink
            v-for="product in products"
            :key="product.to"
            :to="product.to"
            class="flex items-start gap-3 rounded-lg px-3 py-2.5 hover:bg-brand-50 group/item transition-colors"
          >
            <div class="mt-0.5 flex h-7 w-7 items-center justify-center rounded-md bg-brand-50 group-hover/item:bg-brand-100 transition-colors shrink-0">
              <svg class="h-4 w-4 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true" v-html="product.icon" />
            </div>
            <div>
              <div class="text-sm font-medium text-slate-900">{{ product.label }}</div>
              <div class="text-xs text-slate-500 mt-0.5">{{ product.desc }}</div>
            </div>
          </NuxtLink>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
const products = [
  {
    to: '/products/zoho-one',
    label: 'Zoho One',
    desc: 'All-in-one business suite',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />',
  },
  {
    to: '/products/zoho-marketing-plus',
    label: 'Zoho Marketing Plus',
    desc: 'Unified marketing platform',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />',
  },
  {
    to: '/products/zoho-workplace',
    label: 'Zoho Workplace',
    desc: 'Collaboration suite',
    icon: '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />',
  },
]
</script>
