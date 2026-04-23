<template>
  <div class="flex min-h-screen flex-col bg-white">
    <AppHeader />
    <main class="flex-1 pt-16">
      <slot />
    </main>
    <AppFooter />
  </div>
</template>
