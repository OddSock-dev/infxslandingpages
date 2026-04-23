<script setup lang="ts">
defineProps<{ title: string; description?: string }>()
</script>
<template>
  <div>
    <h2 class="text-xl font-bold text-slate-900">{{ title }}</h2>
    <p v-if="description" class="mt-1 text-sm text-slate-500">{{ description }}</p>
    <div class="mt-5">
      <slot />
    </div>
  </div>
</template>
