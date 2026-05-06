@extends('layouts.app', ['page' => $page])

@section('content')
    <section class="pb-20 pt-28 sm:pb-24 sm:pt-32">
        <div class="mx-auto max-w-4xl px-6 lg:px-8">
            <div class="panel legal-copy p-8 sm:p-10" data-reveal>
                <p class="eyebrow">Terms Of Service</p>
                <h1 class="mt-4 font-display text-4xl font-semibold tracking-tight text-slate-950">Terms of Service</h1>
                <p class="mt-3 text-sm text-slate-500">Last updated: {{ $page['body_config']['last_updated'] }}</p>

                <h2>1. Acceptance</h2>
                <p>By using this website, you agree to these terms. If you do not agree, please do not use the site.</p>

                <h2>2. Purpose of the site</h2>
                <p>The site is designed to connect prospective customers with INFX Solutions for Zoho-related consulting, implementation, migration, and optimisation services.</p>

                <h2>3. Enquiries are not contracts</h2>
                <p>Submitting a form or interacting with the qualification journey does not create a binding agreement for services. It is an enquiry and discovery step only.</p>

                <h2>4. Intellectual property</h2>
                <p>Site content, page structure, copy, and branding belong to INFX Solutions or its licensors. Zoho product names and trademarks remain the property of Zoho Corporation Pvt. Ltd.</p>

                <h2>5. Limitation of liability</h2>
                <p>This site is provided on an “as is” basis. We are not liable for indirect or consequential loss arising from the use of the site or reliance on its content.</p>

                <h2>6. Governing law</h2>
                <p>These terms are governed by the laws of the Republic of South Africa.</p>

                <h2>7. Contact</h2>
                <p>For legal questions, contact <a href="mailto:legal@infxsolutions.co.za">legal@infxsolutions.co.za</a>.</p>
            </div>
        </div>
    </section>
@endsection
