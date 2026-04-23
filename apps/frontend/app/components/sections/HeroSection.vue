<script setup lang="ts">
defineProps<{
  headline: string
  subheadline: string
  ctaText?: string
  ctaUrl?: string
  eyebrow?: string
}>()
</script>

<template>
  <section class="relative overflow-hidden bg-white">
    <!-- Subtle dot grid — top-right quadrant only, for visual texture -->
    <div
      class="pointer-events-none absolute right-0 top-0 h-[480px] w-[480px] opacity-40"
      style="background-image: radial-gradient(circle, #CBD5E1 1px, transparent 1px); background-size: 24px 24px;"
    />
    <!-- Accent shape — brand color blob top right -->
    <div class="pointer-events-none absolute -top-32 -right-32 h-[400px] w-[400px] rounded-full bg-brand-50 opacity-60" />

    <div class="relative mx-auto max-w-6xl px-6 pt-24 pb-20 lg:px-8 lg:pt-32 lg:pb-28">
      <!-- Layout: 60/40 split -->
      <div class="grid grid-cols-1 gap-16 lg:grid-cols-5 lg:items-center">
        <!-- Text column -->
        <div class="lg:col-span-3">
          <!-- Eyebrow -->
          <div v-if="eyebrow" class="mb-6">
            <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 border border-brand-100 px-4 py-1.5 text-sm font-medium text-brand-700">
              {{ eyebrow }}
            </span>
          </div>

          <!-- Headline -->
          <h1 class="text-5xl font-bold tracking-tight text-slate-900 sm:text-6xl lg:text-7xl leading-[1.05]">
            {{ headline }}
          </h1>

          <!-- Sub -->
          <p class="mt-6 text-xl text-slate-500 leading-relaxed max-w-xl">
            {{ subheadline }}
          </p>

          <!-- CTAs -->
          <div class="mt-10 flex flex-col sm:flex-row gap-3">
            <a
              v-if="ctaUrl"
              :href="ctaUrl"
              class="inline-flex items-center justify-center gap-2 rounded-xl bg-accent-500 px-7 py-3.5 text-base font-semibold text-white btn-accent hover:bg-accent-600"
            >
              {{ ctaText ?? 'Get Started Free' }}
              <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
              </svg>
            </a>
            <a
              href="#qualify"
              class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-7 py-3.5 text-base font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all"
            >
              How it works
            </a>
          </div>

          <!-- Social proof text -->
          <p class="mt-6 text-sm text-slate-400">
            Free consultation · No credit card · POPIA compliant
          </p>
        </div>

        <!-- Visual column — product dashboard mockup -->
        <div class="lg:col-span-2 mt-10 lg:mt-0">
          <div class="relative">

            <!-- Floating POPIA badge — top right -->
            <div class="absolute -top-4 -right-3 z-10 hidden lg:flex items-center gap-1.5 rounded-xl bg-emerald-500 text-white text-xs font-bold px-3 py-2 shadow-lg">
              <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
              </svg>
              POPIA Compliant
            </div>

            <!-- Browser-chrome dashboard card -->
            <div class="rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/5 overflow-hidden">
              <!-- Chrome bar -->
              <div class="flex items-center gap-2 px-4 py-2.5 bg-slate-50 border-b border-slate-100">
                <div class="flex gap-1.5">
                  <div class="h-2.5 w-2.5 rounded-full bg-red-400"></div>
                  <div class="h-2.5 w-2.5 rounded-full bg-amber-400"></div>
                  <div class="h-2.5 w-2.5 rounded-full bg-emerald-400"></div>
                </div>
                <div class="flex-1 rounded-md bg-white border border-slate-200 h-5 px-2.5 flex items-center mx-1">
                  <span class="text-[10px] text-slate-400 truncate">crm.zoho.com/dashboard</span>
                </div>
              </div>

              <!-- Dashboard content -->
              <div class="p-4 space-y-3">
                <!-- Metric tiles -->
                <div class="grid grid-cols-3 gap-2">
                  <div class="rounded-xl bg-brand-50 px-2 py-2.5 text-center">
                    <div class="text-base font-extrabold text-brand-600">R 2.4M</div>
                    <div class="text-[9px] text-slate-500 mt-0.5 uppercase tracking-wide">Pipeline</div>
                  </div>
                  <div class="rounded-xl bg-accent-50 px-2 py-2.5 text-center">
                    <div class="text-base font-extrabold text-accent-600">↑ 34%</div>
                    <div class="text-[9px] text-slate-500 mt-0.5 uppercase tracking-wide">Conversion</div>
                  </div>
                  <div class="rounded-xl bg-emerald-50 px-2 py-2.5 text-center">
                    <div class="text-base font-extrabold text-emerald-600">98%</div>
                    <div class="text-[9px] text-slate-500 mt-0.5 uppercase tracking-wide">Satisfied</div>
                  </div>
                </div>

                <!-- Mini bar chart -->
                <div class="rounded-xl bg-slate-50 px-3 py-3">
                  <div class="text-[10px] font-semibold text-slate-500 mb-2.5 uppercase tracking-wide">Deals Closed — 6 months</div>
                  <div class="flex items-end gap-1 h-12">
                    <div class="flex-1 rounded-t bg-brand-200 h-4"></div>
                    <div class="flex-1 rounded-t bg-brand-300 h-6"></div>
                    <div class="flex-1 rounded-t bg-brand-400 h-5"></div>
                    <div class="flex-1 rounded-t bg-brand-500 h-9"></div>
                    <div class="flex-1 rounded-t bg-brand-600 h-8"></div>
                    <div class="flex-1 rounded-t bg-brand-700 h-12"></div>
                  </div>
                </div>

                <!-- Recent activity -->
                <div class="space-y-2">
                  <div class="flex items-center gap-2">
                    <div class="h-6 w-6 rounded-full bg-brand-600 flex items-center justify-center text-white text-[10px] font-bold shrink-0">D</div>
                    <div class="text-xs text-slate-600 flex-1 min-w-0 truncate">
                      <span class="font-semibold">Deal closed</span> · Cape Town Retailers
                    </div>
                    <div class="text-xs font-semibold text-emerald-600 shrink-0">+R 45K</div>
                  </div>
                  <div class="flex items-center gap-2">
                    <div class="h-6 w-6 rounded-full bg-accent-500 flex items-center justify-center text-white text-[10px] font-bold shrink-0">L</div>
                    <div class="text-xs text-slate-600 flex-1 min-w-0 truncate">
                      <span class="font-semibold">Lead qualified</span> · Joburg SME
                    </div>
                    <div class="text-xs text-slate-400 shrink-0">2m ago</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Floating clients badge — bottom left -->
            <div class="absolute -bottom-5 -left-3 z-10 hidden lg:flex items-center gap-2.5 rounded-2xl bg-white border border-slate-200 shadow-xl px-4 py-2.5">
              <div class="flex -space-x-2">
                <div class="h-7 w-7 rounded-full bg-brand-600 border-2 border-white flex items-center justify-center text-white text-[10px] font-bold">S</div>
                <div class="h-7 w-7 rounded-full bg-accent-500 border-2 border-white flex items-center justify-center text-white text-[10px] font-bold">J</div>
                <div class="h-7 w-7 rounded-full bg-emerald-600 border-2 border-white flex items-center justify-center text-white text-[10px] font-bold">P</div>
              </div>
              <div>
                <div class="text-sm font-bold text-slate-900">200+ SA businesses</div>
                <div class="text-xs text-slate-500">trust INFX Solutions</div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
</template>
