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
        :primary-action="['label' => $page['cta_text'], 'url' => '#consultation']"
        :secondary-action="['label' => 'Start Free Trial', 'url' => $page['trial_url']]"
    >
        <div id="consultation" class="relative">
            <div class="absolute inset-0 rounded-[2rem] bg-linear-to-br from-teal-300/20 via-transparent to-orange-300/20 blur-2xl"></div>
            <div class="relative">
                <livewire:marketing.product-lead :page-key="$page['page_key']" :product-name="$content['nav_label']" />
            </div>
        </div>
    </x-marketing.hero>

    <section class="pb-16 sm:pb-20">
        <div class="mx-auto grid max-w-7xl gap-8 px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
            <div class="panel p-8" data-reveal>
                <p class="eyebrow">Best Fit</p>
                <h2 class="section-title mt-4">{{ $content['offer_title'] }}</h2>
                <p class="section-copy mt-4">{{ $content['offer_copy'] }}</p>
            </div>

            <div class="panel p-8" data-reveal style="transition-delay: 120ms">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-400">Common Outcomes</p>
                <ul class="mt-6 space-y-4">
                    @foreach($content['highlights'] as $highlight)
                        <li class="flex items-start gap-3 text-sm leading-6 text-slate-700">
                            <span class="mt-1 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.2 7.2a1 1 0 01-1.415.005l-3.2-3.15a1 1 0 011.402-1.43l2.492 2.454 6.496-6.5a1 1 0 011.425 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            <span>{{ $highlight }}</span>
                        </li>
                    @endforeach
                </ul>
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
        :primary="['label' => $page['cta_text'], 'url' => '#consultation']"
        :secondary="['label' => 'Explore Another Product', 'url' => route('home') . '#qualify']"
    />
@endsection
