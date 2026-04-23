<script setup lang="ts">
import { useSeo } from '~/composables/useSeo'

definePageMeta({ layout: 'default' })

useSeo({
  title: 'Zoho One — All-in-One Business Suite | INFX Solutions',
  description: 'Run your entire business on Zoho One. 45+ integrated apps covering CRM, HR, finance, projects, and more. Get a free consultation from INFX Solutions.',
  robots: 'index, follow',
  ogTitle: 'Zoho One | INFX Solutions',
  ogDescription: 'The operating system for business. 45+ Zoho apps, one subscription.',
})
</script>

<template>
  <div>
    <HeroSection
      headline="Run Your Entire Business on Zoho One"
      subheadline="45+ integrated apps — CRM, HR, finance, projects, and more. One subscription, one platform, zero data silos."
      cta-text="Start Free Trial"
      cta-url="https://www.zoho.com/one/signup.html"
    />

    <TrustBadges />

    <section id="quote" class="py-20">
      <div class="mx-auto max-w-6xl px-6 lg:px-8 ">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-start">
          <div>
            <h2 class="text-3xl font-bold text-slate-900">Everything your business needs, together</h2>
            <p class="mt-4 text-slate-500">
              Zoho One is the all-in-one suite that replaces your fragmented software stack. With over 45 business apps
              that share data seamlessly, you get a 360° view of your business in real time.
            </p>
            <ul class="mt-6 space-y-3">
              <li v-for="item in ['CRM & Sales automation', 'HR & Recruitment', 'Finance & Invoicing', 'Project management', 'Marketing automation', 'Customer support']" :key="item" class="flex items-center gap-2 text-slate-700">
                <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg> {{ item }}
              </li>
            </ul>
          </div>
          <div>
            <ProductForm product-key="zoho_one" />
          </div>
        </div>
      </div>
    </section>

    <FeatureGrid
      :features="[
        { icon: 'link', title: 'Unified Platform', description: 'All 45+ apps share the same data model — no integrations to build, no sync errors, no duplicate records.' },
        { icon: 'chart-bar', title: 'Real-Time Analytics', description: 'Cross-app dashboards and reports give you a single source of truth for your entire business.' },
        { icon: 'ai', title: 'AI-Powered (Zia)', description: 'Zoho\'s built-in AI assistant Zia predicts trends, automates workflows, and surfaces insights across all apps.' },
        { icon: 'mobile', title: 'Mobile-First', description: 'All Zoho One apps have polished iOS and Android apps. Your team stays productive on the go.' },
        { icon: 'lock', title: 'Role-Based Access', description: 'Granular permissions and single sign-on across all apps. Control exactly who sees what.' },
        { icon: 'price', title: 'One Price', description: 'One per-user subscription covers all 45+ apps. Significant savings vs. buying individual solutions.' },
      ]"
    />

    <TestimonialBlock
      :testimonials="[
        { quote: 'Zoho One replaced 8 different software tools we were using. The ROI in the first year was remarkable.', author: 'David L.', role: 'CFO, Johannesburg' },
        { quote: 'Implementation was smooth and the INFX team understood our specific workflows from day one.', author: 'Anele M.', role: 'COO, Cape Town' },
      ]"
    />

    <CtaBlock
      cta-text="Request a Free Consultation"
      cta-url="#quote"
      trial-url="https://www.zoho.com/one/signup.html"
    />

    <FaqSection
      :items="[
        { question: 'How many users does Zoho One support?', answer: 'Zoho One is priced per user per month and scales from small teams to large enterprises. There\'s no minimum user requirement to get started.' },
        { question: 'Can I migrate data from my current CRM?', answer: 'Yes. We handle migrations from Salesforce, HubSpot, Microsoft Dynamics, and most other CRMs. Data migration is included in our implementation packages.' },
        { question: 'How long does implementation take?', answer: 'A basic Zoho One setup takes 2–4 weeks. Complex implementations with custom workflows and integrations typically take 6–12 weeks. We\'ll give you a precise timeline after scoping.' },
      ]"
    />
  </div>
</template>
