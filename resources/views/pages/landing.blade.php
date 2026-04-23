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
        :highlights="$content['hero_points']"
        :stats="$content['hero_metrics']"
        :primary-action="['label' => $page['cta_text'], 'url' => $page['cta_url']]"
        :secondary-action="$content['cta_secondary']"
    >
        <div id="qualify" class="relative">
            <div class="absolute inset-0 rounded-[2rem] bg-linear-to-br from-orange-400/20 via-transparent to-teal-300/20 blur-2xl"></div>
            <div class="relative">
                <livewire:marketing.qualifier />
            </div>
        </div>
    </x-marketing.hero>

    <section class="pb-10 sm:pb-14">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-3">
                @foreach($content['trust_marks'] as $index => $trustMark)
                    <div class="panel px-5 py-5" data-reveal style="transition-delay: {{ $index * 90 }}ms">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-950 text-white shadow-soft">
                                <span class="font-display text-sm font-semibold">{{ $index + 1 }}</span>
                            </div>
                            <p class="text-sm font-semibold text-slate-800">{{ $trustMark }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="pb-16 sm:pb-24">
        <div class="mx-auto grid max-w-7xl gap-8 px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
            <div class="panel p-8" data-reveal>
                <p class="eyebrow">How The Journey Works</p>
                <h2 class="section-title mt-4">A shorter path from curiosity to a confident Zoho recommendation.</h2>
                <p class="section-copy mt-4">
                    The public site is designed to feel like a guided buying experience, not a brochure. Every step reduces decision fatigue and keeps the final lead richer for the team following up.
                </p>
            </div>

            <div class="grid gap-4">
                @foreach($content['journey_steps'] as $index => $step)
                    <article class="panel flex gap-5 p-6 sm:p-7" data-reveal style="transition-delay: {{ $index * 120 }}ms">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-orange-400 to-amber-500 text-base font-bold text-white shadow-soft">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-950">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $step['description'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <x-marketing.feature-grid
        :title="$content['feature_heading']"
        :subtitle="$content['feature_subheading']"
        :features="$content['features']"
    />

    <x-marketing.testimonial-grid :items="$content['testimonials']" />

    <x-marketing.faq-list :items="$content['faq']" />

    <x-marketing.cta-band
        :headline="$content['cta_heading']"
        :copy="$content['cta_copy']"
        :primary="$content['cta_primary']"
        :secondary="$content['cta_secondary']"
    />
@endsection
