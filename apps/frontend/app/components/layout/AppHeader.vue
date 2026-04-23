<script setup lang="ts">
const { y } = useWindowScroll()
const isScrolled = computed(() => y.value > 20)
</script>

<template>
  <header
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-200"
    :class="isScrolled
      ? 'bg-white shadow-sm border-b border-slate-100'
      : 'bg-white/80 backdrop-blur-sm'"
  >
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4 lg:px-8">
      <!-- Logo -->
      <NuxtLink to="/" class="flex items-center gap-2.5 group">
        <div class="h-8 w-8 rounded-lg bg-brand-600 flex items-center justify-center shadow-sm">
          <span class="text-sm font-black text-white tracking-tight">IX</span>
        </div>
        <span class="text-base font-semibold text-slate-900 group-hover:text-brand-600 transition-colors">
          INFX Solutions
        </span>
      </NuxtLink>

      <!-- Nav -->
      <SiteNav />

      <!-- CTA — plain <a> so Nuxt doesn't try to prefetch /#qualify as a route -->
      <a
        href="/#qualify"
        class="hidden sm:inline-flex items-center gap-1.5 rounded-lg bg-accent-500 px-4 py-2 text-sm font-semibold text-white btn-accent hover:bg-accent-600"
      >
        Get Free Quote
      </a>
    </div>
  </header>
</template>
