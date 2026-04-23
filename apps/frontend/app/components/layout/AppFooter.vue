<script setup lang="ts">
const year = new Date().getFullYear()
const products = [
  { to: '/products/zoho-one', label: 'Zoho One' },
  { to: '/products/zoho-marketing-plus', label: 'Zoho Marketing Plus' },
  { to: '/products/zoho-workplace', label: 'Zoho Workplace' },
]
</script>

<template>
  <footer class="border-t border-slate-200 bg-slate-50">
    <div class="mx-auto max-w-6xl px-6 py-16 lg:px-8">
      <div class="grid grid-cols-1 gap-12 md:grid-cols-4">
        <!-- Brand -->
        <div class="col-span-1 md:col-span-2">
          <div class="flex items-center gap-2.5 mb-4">
            <div class="h-8 w-8 rounded-lg bg-brand-600 flex items-center justify-center">
              <span class="text-sm font-black text-white">IX</span>
            </div>
            <span class="text-base font-semibold text-slate-900">INFX Solutions</span>
          </div>
          <p class="text-sm text-slate-500 max-w-xs leading-relaxed">
            South Africa's trusted Zoho Authorised Partner — helping businesses find, implement, and maximise their Zoho investment since 2019.
          </p>
          <div class="mt-5 flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white border border-slate-200 px-3 py-1 text-xs font-medium text-slate-700 shadow-xs">
              <svg class="h-3 w-3 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-.702 3.642 3.745 3.745 0 01-3.642.702A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 01-3.642-.702 3.745 3.745 0 01-.702-3.642A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 01.702-3.642 3.745 3.745 0 013.642-.702A3.745 3.745 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 013.642.702 3.745 3.745 0 01.702 3.642A3.745 3.745 0 0121 12z" />
              </svg>
              Zoho Authorised Partner
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white border border-slate-200 px-3 py-1 text-xs font-medium text-slate-700 shadow-xs">
              <svg class="h-3 w-3 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
              </svg>
              SA-Based Support
            </span>
          </div>
        </div>

        <!-- Products -->
        <div>
          <h3 class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-5">Products</h3>
          <ul class="space-y-3.5">
            <li v-for="link in products" :key="link.to">
              <NuxtLink :to="link.to" class="text-sm text-slate-600 hover:text-brand-600 transition-colors">
                {{ link.label }}
              </NuxtLink>
            </li>
          </ul>
        </div>

        <!-- Legal -->
        <div>
          <h3 class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-5">Legal</h3>
          <ul class="space-y-3.5">
            <li>
              <NuxtLink to="/privacy" class="text-sm text-slate-600 hover:text-brand-600 transition-colors">Privacy Policy</NuxtLink>
            </li>
            <li>
              <NuxtLink to="/terms" class="text-sm text-slate-600 hover:text-brand-600 transition-colors">Terms of Service</NuxtLink>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Bottom bar -->
    <div class="border-t border-slate-200 bg-white py-5">
      <div class="mx-auto max-w-6xl px-6 flex flex-col sm:flex-row items-center justify-between gap-3 lg:px-8">
        <p class="text-xs text-slate-400">© {{ year }} INFX Solutions (Pty) Ltd. All rights reserved.</p>
        <p class="text-xs text-slate-400">Zoho® is a trademark of Zoho Corporation Pvt. Ltd.</p>
      </div>
    </div>
  </footer>
</template>
