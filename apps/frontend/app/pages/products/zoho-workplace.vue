<script setup lang="ts">
import { useSeo } from '~/composables/useSeo'

definePageMeta({ layout: 'default' })

useSeo({
  title: 'Zoho Workplace — Collaboration Suite | INFX Solutions',
  description: 'Email, chat, video conferencing, docs, and intranet — all in Zoho Workplace. A modern alternative to Google Workspace and Microsoft 365.',
  robots: 'index, follow',
  ogTitle: 'Zoho Workplace | INFX Solutions',
  ogDescription: 'Modern collaboration suite for teams of all sizes.',
})
</script>

<template>
  <div>
    <HeroSection
      headline="Collaborate Seamlessly with Zoho Workplace"
      subheadline="Email, chat, video meetings, docs, and intranet — a privacy-first alternative to Google Workspace and Microsoft 365."
      cta-text="Start Free Trial"
      cta-url="https://www.zoho.com/workplace/signup.html"
    />

    <TrustBadges />

    <section id="quote" class="py-20">
      <div class="mx-auto max-w-6xl px-6 lg:px-8 ">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-start">
          <div>
            <h2 class="text-3xl font-bold text-slate-900">Everything your team needs to work together</h2>
            <p class="mt-4 text-slate-500">
              Zoho Workplace gives your team a unified collaboration hub — professional email, instant messaging,
              video conferencing, document collaboration, and a company intranet, all under one subscription.
            </p>
            <ul class="mt-6 space-y-3">
              <li v-for="item in ['Professional email (Zoho Mail)', 'Instant messaging & channels', 'Video conferencing', 'Document collaboration', 'Company intranet', 'File storage & sharing']" :key="item" class="flex items-center gap-2 text-slate-700">
                <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg> {{ item }}
              </li>
            </ul>
          </div>
          <div>
            <ProductForm product-key="zoho_workplace" />
          </div>
        </div>
      </div>
    </section>

    <FeatureGrid
      :features="[
        { icon: 'email', title: 'Zoho Mail', description: 'Ad-free, privacy-first email with powerful rules, filters, and a clean interface. Custom domain email for your whole team.' },
        { icon: 'chat', title: 'Zoho Cliq', description: 'Channels, threads, and direct messages. Integrates with your other Zoho apps so conversations are connected to context.' },
        { icon: 'video', title: 'Zoho Meeting', description: 'HD video conferencing with screen sharing, recording, and breakout rooms. No download required for guests.' },
        { icon: 'document', title: 'Zoho Writer', description: 'Collaborative document editing in real time. Works with .docx files and integrates with the rest of Zoho Workplace.' },
        { icon: 'home', title: 'Zoho Connect', description: 'Company intranet and social network. Share news, policies, and knowledge across your organisation.' },
        { icon: 'privacy', title: 'Privacy-First', description: 'Zoho does not sell your data or use it for advertising. POPIA and GDPR compliant by design.' },
      ]"
    />

    <TestimonialBlock
      :testimonials="[
        { quote: 'We migrated 120 users from Google Workspace to Zoho Workplace in a weekend. INFX managed everything.', author: 'Brendan T.', role: 'IT Manager, Pretoria' },
        { quote: 'The cost saving vs Microsoft 365 was significant. And the privacy-first approach fits our values.', author: 'Nomsa D.', role: 'CTO, Fintech Startup' },
      ]"
    />

    <CtaBlock
      cta-text="Request a Free Consultation"
      cta-url="#quote"
      trial-url="https://www.zoho.com/workplace/signup.html"
    />

    <FaqSection
      :items="[
        { question: 'Can we migrate from Google Workspace or Microsoft 365?', answer: 'Yes. We handle email migrations, contact and calendar imports, and can migrate existing files to Zoho WorkDrive. Migration is included in our implementation packages.' },
        { question: 'Is there a minimum user count?', answer: 'Zoho Workplace starts from 1 user. You pay per user per month and can add or remove users at any time.' },
        { question: 'Does Zoho Workplace work offline?', answer: 'Zoho Mail and Zoho Writer have offline modes. Zoho Cliq mobile apps also cache recent messages for offline access.' },
      ]"
    />
  </div>
</template>
