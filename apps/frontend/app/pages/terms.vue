<script setup lang="ts">
import { useSeo } from '~/composables/useSeo'

definePageMeta({ layout: 'minimal' })

useSeo({
  title: 'Terms of Service | INFX Solutions',
  description: 'INFX Solutions terms of service for use of our website and services.',
  robots: 'index, follow',
})
</script>

<template>
  <div class="prose prose-gray max-w-none">
    <h1>Terms of Service</h1>
    <p class="text-gray-500">Last updated: April 2026</p>

    <h2>1. Acceptance</h2>
    <p>By using this website you agree to these Terms of Service. If you do not agree, please do not use the site.</p>

    <h2>2. Services</h2>
    <p>
      INFX Solutions (Pty) Ltd is a Zoho Authorised Partner. This website is used to connect prospective customers
      with our Zoho consulting and implementation services. Submitting a form constitutes an enquiry only — it does
      not constitute a binding agreement for services.
    </p>

    <h2>3. Intellectual property</h2>
    <p>
      All content on this site (text, images, logos) is owned by INFX Solutions or its licensors. Zoho product names
      and logos are trademarks of Zoho Corporation Pvt. Ltd.
    </p>

    <h2>4. Limitation of liability</h2>
    <p>
      INFX Solutions provides this website "as is". We are not liable for any indirect, incidental, or consequential
      damages arising from your use of this site.
    </p>

    <h2>5. Governing law</h2>
    <p>These terms are governed by the laws of the Republic of South Africa.</p>

    <h2>6. Contact</h2>
    <p>INFX Solutions (Pty) Ltd — <a href="mailto:legal@infxsolutions.co.za">legal@infxsolutions.co.za</a></p>
  </div>
</template>
