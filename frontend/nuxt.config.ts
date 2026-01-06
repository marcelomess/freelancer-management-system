// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
  ],

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://localhost/api',
      wsHost: process.env.NUXT_PUBLIC_WS_HOST || 'localhost',
      wsPort: process.env.NUXT_PUBLIC_WS_PORT || 8080,
    }
  },

  ssr: false, // Cliente-side rendering conforme especificado
  
  app: {
    head: {
      title: 'Freelancer Management System',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'description', content: 'Sistema de gestão de freelancers' }
      ]
    }
  }
})
