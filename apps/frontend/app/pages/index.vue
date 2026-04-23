<script setup lang="ts">
import { useSeo } from '~/composables/useSeo'

useSeo({
  title: 'Find Your Perfect Zoho Solution | INFX Solutions',
  description: 'South Africa\'s trusted Zoho Authorised Partner. Tell us what you need — free consultation, no obligation.',
  canonicalUrl: 'https://zoho.infxsolutions.co.za/',
})
</script>

<template>
  <div>
    <HeroSection
      headline="Find the Right Zoho Solution for Your Business"
      subheadline="South Africa's trusted Zoho Authorised Partner. Tell us what you need — we'll find the perfect fit."
      eyebrow="South Africa's #1 Zoho Authorised Partner"
      cta-text="Find My Solution"
      cta-url="#qualify"
    />

    <TrustBadges />

    <!-- Stats section -->
    <section class="bg-white py-16">
      <div class="mx-auto max-w-6xl px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-8 lg:grid-cols-4">
          <div v-for="stat in [
            { value: '200+', suffix: '', label: 'SA Businesses Served' },
            { value: '45+', suffix: '', label: 'Zoho Apps, One Subscription' },
            { value: '7+', suffix: '', label: 'Years Zoho Expertise' },
            { value: '98', suffix: '%', label: 'Customer Satisfaction' },
          ]" :key="stat.label" class="text-center">
            <div class="text-4xl font-black tracking-tight text-slate-900">
              {{ stat.value }}<span class="text-brand-600">{{ stat.suffix }}</span>
            </div>
            <div class="mt-1 text-sm text-slate-500">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Qualify section -->
    <section id="qualify" class="bg-slate-50 py-24">
      <div class="mx-auto max-w-6xl px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-12 lg:grid-cols-2 lg:items-start">
          <!-- Left: copy -->
          <div class="lg:pt-4">
            <p class="text-xs font-semibold uppercase tracking-widest text-brand-600 mb-3">Free · 2 Minutes</p>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
              Which Zoho product is right for you?
            </h2>
            <p class="mt-4 text-lg text-slate-500 leading-relaxed">
              Answer two quick questions and we'll instantly match you to the right Zoho solution — then connect you with a local expert for a free consultation.
            </p>
            <div class="mt-8 space-y-4">
              <div v-for="step in [
                { n: '1', title: 'Tell us what you need', desc: 'Pick the Zoho product that fits your goals' },
                { n: '2', title: 'Confirm your interest', desc: 'Give us permission to reach out' },
                { n: '3', title: 'We match & contact you', desc: 'A certified Zoho expert will call within 24h' },
              ]" :key="step.n" class="flex items-start gap-4">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">
                  {{ step.n }}
                </div>
                <div>
                  <div class="text-sm font-semibold text-slate-900">{{ step.title }}</div>
                  <div class="text-sm text-slate-500">{{ step.desc }}</div>
                </div>
              </div>
            </div>
          </div>
          <!-- Right: form card -->
          <div>
            <QualificationForm />
          </div>
        </div>
      </div>
    </section>

    <FeatureGrid
      title="Why businesses choose INFX Solutions"
      subtitle="We don't just sell Zoho — we implement it, train your team, and support you long-term."
      :features="[
        { icon: 'building', title: 'Zoho One', description: 'Over 45 integrated apps for your entire business — CRM, HR, finance, projects and more in a single subscription.' },
        { icon: 'megaphone', title: 'Zoho Marketing Plus', description: 'Unified marketing platform. Manage campaigns, social media, webinars, and customer journeys from one dashboard.' },
        { icon: 'chat', title: 'Zoho Workplace', description: 'Collaboration suite with email, chat, docs, video conferencing, and intranet — everything your team needs.' },
        { icon: 'map-pin', title: 'SA-Based Support', description: 'Our team is based in South Africa and understands local business needs, time zones, and POPIA compliance.' },
        { icon: 'shield', title: 'Enterprise Security', description: 'Zoho products are ISO 27001 certified with GDPR/POPIA-ready data handling and end-to-end encryption.' },
        { icon: 'bolt', title: 'Fast Implementation', description: 'We get you up and running quickly with tailored onboarding, training, and ongoing support packages.' },
      ]"
    />

    <TestimonialBlock
      :testimonials="[
        { quote: 'INFX Solutions transformed how our business operates. Zoho One has everything we need in one place.', author: 'Sarah M.', role: 'CEO, Cape Town SME' },
        { quote: 'The team made our Zoho Marketing Plus rollout seamless. Our lead generation has improved significantly.', author: 'James K.', role: 'Marketing Director' },
        { quote: 'Excellent local support and deep Zoho expertise. We\'ve been with INFX Solutions for 3 years.', author: 'Priya N.', role: 'Operations Manager' },
      ]"
    />

    <CtaBlock
      cta-text="Book Free Consultation"
      cta-url="/#qualify"
      headline="Ready to transform your business with Zoho?"
      subheadline="Book a free 15-minute discovery call with a certified Zoho expert. No obligation, no pressure."
    />

    <FaqSection
      :items="[
        { question: 'What is a Zoho Authorised Partner?', answer: 'A Zoho Authorised Partner is an official reseller and implementation expert certified by Zoho Corporation. We have direct access to Zoho support, pre-release information, and can offer local pricing in ZAR.' },
        { question: 'How does the free consultation work?', answer: 'You select the Zoho product you\'re interested in, fill in your details, and one of our certified Zoho experts will call you within one business day for a 15-minute discovery conversation — no obligation.' },
        { question: 'Is Zoho POPIA compliant?', answer: 'Yes. Zoho\'s data centres and products are designed with GDPR and POPIA compliance in mind. All data for South African customers can be stored in Zoho\'s data centre regions that meet local requirements.' },
        { question: 'Can I switch from another CRM or platform?', answer: 'Absolutely. We specialise in data migration from Salesforce, HubSpot, Microsoft 365, Google Workspace, and many other platforms. Most migrations are completed within days with zero data loss.' },
      ]"
    />
  </div>
</template>
