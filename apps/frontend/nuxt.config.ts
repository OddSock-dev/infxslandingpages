import tailwindcss from '@tailwindcss/vite'

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  modules: ['@pinia/nuxt', '@vueuse/nuxt'],

  css: ['~/assets/css/main.css'],

  vite: {
    plugins: [tailwindcss()],
  },

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE ?? 'http://localhost:8000/api',
    },
  },

  nitro: {
    prerender: {
      routes: [
        '/',
        '/products/zoho-marketing-plus',
        '/products/zoho-one',
        '/products/zoho-workplace',
        '/thanks',
        '/privacy',
        '/terms',
      ],
    },
  },
})
