<script setup lang="ts">
defineProps<{
  testimonials: Array<{ quote: string; author: string; role?: string }>
}>()

function initials(name: string): string {
  return name.split(' ').map((n: string) => n[0]).join('').toUpperCase().slice(0, 2)
}

const avatarColors = [
  'bg-brand-600',
  'bg-accent-500',
  'bg-emerald-600',
  'bg-violet-600',
]
</script>

<template>
  <section class="bg-slate-50 py-24">
    <div class="mx-auto max-w-6xl px-6 lg:px-8">
      <div class="mb-14">
        <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
          Trusted by businesses across South Africa
        </h2>
        <p class="mt-3 text-lg text-slate-500">Real results from real clients.</p>
      </div>
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="(t, i) in testimonials"
          :key="t.author"
          class="card p-7 flex flex-col"
        >
          <!-- Stars -->
          <div class="flex gap-0.5 mb-5">
            <svg v-for="s in 5" :key="s" class="h-4 w-4 text-amber-400 fill-current" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
          </div>
          <p class="text-slate-700 leading-relaxed text-sm flex-1">{{ t.quote }}</p>
          <div class="flex items-center gap-3 mt-6 pt-5 border-t border-slate-100">
            <div
              class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-bold text-white shrink-0"
              :class="avatarColors[i % avatarColors.length]"
            >
              {{ initials(t.author) }}
            </div>
            <div>
              <div class="text-sm font-semibold text-slate-900">{{ t.author }}</div>
              <div v-if="t.role" class="text-xs text-slate-500">{{ t.role }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
