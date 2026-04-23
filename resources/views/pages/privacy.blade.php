@extends('layouts.app', ['page' => $page])

@section('content')
    <section class="pb-20 pt-8 sm:pb-24">
        <div class="mx-auto max-w-4xl px-6 lg:px-8">
            <div class="panel legal-copy p-8 sm:p-10" data-reveal>
                <p class="eyebrow">Privacy Policy</p>
                <h1 class="mt-4 font-display text-4xl font-semibold tracking-tight text-slate-950">Privacy Policy</h1>
                <p class="mt-3 text-sm text-slate-500">Last updated: {{ $page['body_config']['last_updated'] }}</p>

                <h2>1. Who we are</h2>
                <p>INFX Solutions (Pty) Ltd is a Zoho Authorised Partner based in South Africa. This site is used to guide prospective buyers toward the right Zoho solution and capture consultation requests.</p>

                <h2>2. What data we collect</h2>
                <p>When you interact with our forms, we may collect your name, email address, phone number, company name, product interest, qualification answers, and operational metadata such as UTM parameters, referrer, user agent, and a hashed IP value.</p>

                <h2>3. Why we process it</h2>
                <p>We use this information to respond to your enquiry, recommend the right Zoho solution, improve campaign performance, and sync qualified submissions into our CRM for appropriate follow-up.</p>

                <h2>4. POPIA and consent</h2>
                <p>We rely on your consent when you ask to be contacted. You may withdraw that consent by contacting <a href="mailto:privacy@infxsolutions.co.za">privacy@infxsolutions.co.za</a>.</p>

                <h2>5. Data retention</h2>
                <p>We keep enquiry and journey data only as long as needed for follow-up, delivery, compliance, and reporting, or until you ask us to remove it where applicable.</p>

                <h2>6. Your rights</h2>
                <p>You may request access to, correction of, or deletion of your personal information in line with POPIA by contacting <a href="mailto:privacy@infxsolutions.co.za">privacy@infxsolutions.co.za</a>.</p>

                <h2>7. Contact</h2>
                <p>For privacy-related questions, contact <a href="mailto:privacy@infxsolutions.co.za">privacy@infxsolutions.co.za</a>.</p>
            </div>
        </div>
    </section>
@endsection
