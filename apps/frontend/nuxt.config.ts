import tailwindcss from '@tailwindcss/vite'

// Production API URL must be set via NUXT_PUBLIC_API_BASE before running
// `npx nuxt generate`. The value is baked into the static bundle at build time.
// Example: NUXT_PUBLIC_API_BASE=https://api.infxsolutions.co.za/api npx nuxt generate

export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  modules: ['@pinia/nuxt', '@vueuse/nuxt'],
  css: ['~/assets/css/main.css'],
  components: [{ path: '~/components', pathPrefix: false }],
  vite: { plugins: [tailwindcss()] },

  runtimeConfig: {
    public: {
      // Override with NUXT_PUBLIC_API_BASE env var at generate time
      apiBase: process.env.NUXT_PUBLIC_API_BASE ?? 'http://localhost:8000/api',
    },
  },

  app: {
    head: {
      htmlAttrs: { lang: 'en-ZA' },
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { property: 'og:site_name', content: 'INFX Solutions' },
        { property: 'og:type', content: 'website' },
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
        {
          rel: 'stylesheet',
          href: 'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300..900;1,14..32,300..900&display=swap',
        },
      ],
    },
  },

  // Static site generation — no Node.js server required.
  // Output lands in .output/public/ — upload that directory to Apache/cPanel.
  nitro: {
    prerender: {
      // Crawl from root + explicitly list all routes to ensure full coverage
      crawlLinks: true,
      routes: [
        '/',
        '/products/zoho-one',
        '/products/zoho-marketing-plus',
        '/products/zoho-workplace',
        '/thanks',
        '/privacy',
        '/terms',
      ],
    },
  },
})
