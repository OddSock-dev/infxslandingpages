<script setup lang="ts">
import { useSeo } from '~/composables/useSeo'

definePageMeta({ layout: 'default' })

useSeo({
  title: 'Zoho Marketing Plus — Unified Marketing Platform | INFX Solutions',
  description: 'Run all your marketing campaigns from one platform. Email, social media, webinars, events, and customer journeys — unified in Zoho Marketing Plus.',
  robots: 'index, follow',
  ogTitle: 'Zoho Marketing Plus | INFX Solutions',
  ogDescription: 'The unified marketing platform for growing businesses.',
})
</script>

<template>
  <div>
    <HeroSection
      headline="Unify Your Marketing with Zoho Marketing Plus"
      subheadline="Email campaigns, social media, webinars, events, and customer journeys — all in one platform. No more tool sprawl."
      cta-text="Start Free Trial"
      cta-url="https://www.zoho.com/marketingplus/signup.html"
    />

    <TrustBadges />

    <section id="quote" class="py-20">
      <div class="mx-auto max-w-6xl px-6 lg:px-8 ">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-start">
          <div>
            <h2 class="text-3xl font-bold text-slate-900">Everything marketing, one subscription</h2>
            <p class="mt-4 text-slate-500">
              Zoho Marketing Plus combines email marketing, social media management, webinar hosting, event management,
              and customer journey automation into a single, unified platform.
            </p>
            <ul class="mt-6 space-y-3">
              <li v-for="item in ['Email & SMS campaigns', 'Social media management', 'Webinar & event hosting', 'Customer journey builder', 'Marketing attribution', 'A/B testing']" :key="item" class="flex items-center gap-2 text-slate-700">
                <svg class="h-4 w-4 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg> {{ item }}
              </li>
            </ul>
          </div>
          <div>
            <ProductForm product-key="zoho_marketing_plus" />
          </div>
        </div>
      </div>
    </section>

    <FeatureGrid
      :features="[
        { icon: 'email', title: 'Email Marketing', description: 'Drag-and-drop email builder, advanced segmentation, and detailed analytics. Send the right message to the right audience.' },
        { icon: 'share', title: 'Social Media', description: 'Schedule, publish, and monitor posts across all major social platforms from a single dashboard.' },
        { icon: 'video', title: 'Webinars & Events', description: 'Host live and on-demand webinars for up to 3,000 attendees. Manage in-person events with ticketing and check-in.' },
        { icon: 'journey', title: 'Customer Journeys', description: 'Visual journey builder lets you automate multi-channel touchpoints based on customer behaviour.' },
        { icon: 'attribution', title: 'Attribution', description: 'Understand which channels drive conversions with cross-channel attribution reports and ROI dashboards.' },
        { icon: 'beaker', title: 'A/B Testing', description: 'Test subject lines, content, send times, and audience segments to continually improve campaign performance.' },
      ]"
    />

    <TestimonialBlock
      :testimonials="[
        { quote: 'We replaced four separate tools with Zoho Marketing Plus. Our campaign management time halved.', author: 'Leigh S.', role: 'Head of Marketing, Durban' },
        { quote: 'The customer journey builder is powerful. We\'ve seen a 40% improvement in email engagement.', author: 'Thabo N.', role: 'Digital Marketing Manager' },
      ]"
    />

    <CtaBlock
      cta-text="Request a Free Consultation"
      cta-url="#quote"
      trial-url="https://www.zoho.com/marketingplus/signup.html"
    />

    <FaqSection
      :items="[
        { question: 'How does Zoho Marketing Plus differ from Zoho Campaigns?', answer: 'Zoho Campaigns is a standalone email marketing tool. Marketing Plus is a unified platform that adds social media, webinars, events, and journey automation on top of email — all with shared data and reporting.' },
        { question: 'Can I integrate it with our CRM?', answer: 'Yes. Zoho Marketing Plus integrates natively with Zoho CRM and Zoho One, and offers integrations with Salesforce and HubSpot via Zoho Flow.' },
        { question: 'What is the subscriber limit?', answer: 'Zoho Marketing Plus is priced by users (marketers), not subscribers. You can have unlimited contacts in your list.' },
      ]"
    />
  </div>
</template>
