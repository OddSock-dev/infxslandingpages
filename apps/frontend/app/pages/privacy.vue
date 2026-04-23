<script setup lang="ts">
import { useSeo } from '~/composables/useSeo'

definePageMeta({ layout: 'minimal' })

useSeo({
  title: 'Privacy Policy | INFX Solutions',
  description: 'INFX Solutions privacy policy — how we collect, use, and protect your personal information.',
  robots: 'index, follow',
})
</script>

<template>
  <div class="prose prose-gray max-w-none">
    <h1>Privacy Policy</h1>
    <p class="text-gray-500">Last updated: April 2026</p>

    <h2>1. Who we are</h2>
    <p>
      INFX Solutions (Pty) Ltd ("we", "us", "our") is a Zoho Authorised Partner based in South Africa.
      Our website address is <a href="https://zoho.infxsolutions.co.za">zoho.infxsolutions.co.za</a>.
    </p>

    <h2>2. What data we collect</h2>
    <p>When you complete a form on our website, we collect:</p>
    <ul>
      <li>Name, email address, phone number, and company name</li>
      <li>Your product interest and qualification answers</li>
      <li>Technical data: IP address (hashed), browser user agent, referrer URL, and UTM campaign parameters</li>
    </ul>

    <h2>3. How we use your data</h2>
    <p>We use your data to:</p>
    <ul>
      <li>Contact you about Zoho products and services you've expressed interest in</li>
      <li>Pass your details to Zoho CRM for follow-up by our sales team</li>
      <li>Improve our website and marketing effectiveness</li>
    </ul>

    <h2>4. Legal basis (POPIA)</h2>
    <p>
      We process your personal information on the basis of your consent, given when you submit a form on this website.
      You may withdraw consent at any time by contacting us at <a href="mailto:privacy@infxsolutions.co.za">privacy@infxsolutions.co.za</a>.
    </p>

    <h2>5. Data retention</h2>
    <p>We retain your personal information for up to 3 years from the date of collection, unless you request deletion sooner.</p>

    <h2>6. Your rights</h2>
    <p>Under POPIA you have the right to access, correct, and request deletion of your personal information. Contact us at <a href="mailto:privacy@infxsolutions.co.za">privacy@infxsolutions.co.za</a>.</p>

    <h2>7. Contact</h2>
    <p>INFX Solutions (Pty) Ltd — <a href="mailto:privacy@infxsolutions.co.za">privacy@infxsolutions.co.za</a></p>
  </div>
</template>
