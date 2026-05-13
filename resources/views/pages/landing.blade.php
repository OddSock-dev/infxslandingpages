@extends('layouts.app', ['page' => $page])

@php
    /** @var array<string, mixed> $page */
    $content = $page['body_config'];
@endphp

@section('content')
    <x-marketing.hero
        :eyebrow="$content['eyebrow']"
        :headline="$content['headline']"
        :subheadline="$content['subheadline']"
        :primary-action="['label' => $page['cta_text'], 'url' => $page['cta_url']]"
        :secondary-action="$content['cta_secondary']"
    >
        <div id="qualify" class="relative mx-auto w-full max-w-124 lg:mx-0 lg:ml-auto">
            <div class="absolute inset-0 rounded-4xl bg-linear-to-br from-teal-400/14 via-transparent to-cyan-400/10 blur-2xl"></div>
            <div class="relative z-10">
                <livewire:marketing.qualifier />
            </div>
        </div>
    </x-marketing.hero>

    <div class="relative z-10 bg-canvas pt-10 sm:pt-12 lg:pt-16">
        <x-marketing.trust-block
            eyebrow="Why this approach works"
            :title="data_get($content, 'trust_heading')"
            :copy="data_get($content, 'trust_copy')"
            :items="data_get($content, 'trust_blocks', [])"
            intro-class="max-w-3xl lg:max-w-[40rem] xl:max-w-[44rem]"
            items-class="mt-10 grid max-w-none gap-4 md:grid-cols-3 lg:max-w-[40rem] lg:grid-cols-2 xl:max-w-[44rem]"
            section-class="pb-12 pt-8 sm:pb-16 sm:pt-10 lg:pt-20"
            :span-last-odd-item="true"
        />
    </div>

    <section class="pb-16 sm:pb-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-4xl border border-slate-200/80 bg-linear-to-br from-white/95 via-white/92 to-teal-50/80 px-6 py-6 shadow-[0_28px_70px_rgba(15,23,42,0.08)] sm:px-8 sm:py-8">
                <div class="pointer-events-none absolute inset-x-0 top-0 h-40 bg-[radial-gradient(circle_at_top_left,rgba(61,224,191,0.12),transparent_60%),radial-gradient(circle_at_top_right,rgba(56,189,248,0.09),transparent_55%)]"></div>
                <div class="relative grid gap-8 lg:grid-cols-[0.92fr_1.08fr]">
                    <div class="relative overflow-hidden rounded-[1.75rem] border border-slate-200/80 bg-slate-950 shadow-hero" data-reveal>
                        <div class="absolute left-5 top-5 z-10 rounded-full border border-white/15 bg-slate-950/70 px-4 py-2 text-[0.65rem] font-semibold uppercase tracking-[0.28em] text-white">
                            Quick business check
                        </div>
                        <img src="{{ asset(data_get($content, 'spotlight.media_url', 'media/systems-audit.webp')) }}" alt="{{ data_get($content, 'spotlight.media_alt', 'Business systems audit illustration') }}" class="h-full min-h-80 w-full object-cover">
                    </div>

                    <div class="space-y-4">
                        <div class="panel panel-strong p-8" data-reveal>
                            <p class="eyebrow">{{ data_get($content, 'spotlight.eyebrow', 'How it works') }}</p>
                            <h2 class="section-title mt-4 text-balance">{{ data_get($content, 'spotlight.title', 'A simpler way to see which Zoho option deserves your attention.') }}</h2>
                            <p class="section-copy mt-4">{{ data_get($content, 'spotlight.copy') }}</p>
                            <div class="mt-6">
                                <a href="#qualify" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white shadow-soft transition hover:-translate-y-0.5 hover:bg-slate-900">
                                    Start the questionnaire
                                </a>
                            </div>
                        </div>

                        @foreach($content['journey_steps'] as $index => $step)
                            <article class="panel panel-strong flex gap-5 p-6 sm:p-7" data-reveal style="transition-delay: {{ $index * 120 }}ms">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-teal-500 to-cyan-500 text-base font-bold text-white shadow-soft">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-950 text-balance">{{ $step['title'] }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $step['description'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-marketing.logo-bank
        eyebrow="Explore the Zoho options"
        :title="data_get($content, 'logo_bank.title')"
        :copy="data_get($content, 'logo_bank.copy')"
        :items="data_get($content, 'logo_bank.items', [])"
    />

    <x-marketing.outcome-grid
        eyebrow="What this helps you do"
        :title="data_get($content, 'outcome_heading')"
        :copy="data_get($content, 'outcome_copy')"
        :items="data_get($content, 'outcomes', [])"
    />

    <x-marketing.feature-grid
        :title="$content['feature_heading']"
        :subtitle="$content['feature_subheading']"
        :features="$content['features']"
    />

    <x-marketing.faq-list
        :items="$content['faq']"
        :title="data_get($content, 'faq_heading', 'Questions buyers usually need answered before they convert.')"
        :copy="data_get($content, 'faq_copy', 'The goal is clarity and confidence, not over-explaining the platform.')"
    />

    <x-marketing.cta-band
        :headline="$content['cta_heading']"
        :copy="$content['cta_copy']"
        :primary="$content['cta_primary']"
        :secondary="$content['cta_secondary']"
    />
@endsection
