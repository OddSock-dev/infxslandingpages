@extends('layouts.app', ['page' => $page])

@php
    /** @var array<string, mixed> $page */
    $content = $page['body_config'];
    $heroAction = [
        'label' => request()->filled('t') ? 'Review My Next Steps' : 'Qualify for a Free Trial',
        'url' => '#consultation',
    ];
    $heroSecondaryAction = filled($page['cta_url'] ?? null) && data_get($page, 'cta_url') !== '#consultation'
        ? ['label' => $page['cta_text'] ?? 'Learn More', 'url' => $page['cta_url']]
        : null;
    $spotlightUsesContain = data_get($content, 'spotlight.media_fit') === 'contain';
    $spotlightLogo = data_get($content, 'hero_logo_block.items.0');
    $spotlightFrameClasses = $spotlightUsesContain
        ? 'relative overflow-hidden rounded-[1.75rem] border border-slate-200/70 bg-linear-to-br from-white via-white to-teal-50 shadow-hero'
        : 'relative overflow-hidden rounded-[1.75rem] border border-slate-200/70 bg-slate-950 shadow-hero';
    $spotlightBadgeClasses = $spotlightUsesContain
        ? 'absolute left-5 top-5 z-10 inline-flex items-center gap-3 rounded-full border border-slate-200 bg-white/95 px-4 py-2 shadow-[0_12px_30px_rgba(15,23,42,0.12)]'
        : 'absolute left-5 top-5 z-10 inline-flex items-center gap-3 rounded-full border border-white/15 bg-slate-950/70 px-4 py-2';
    $spotlightBadgeTextClasses = $spotlightUsesContain ? 'text-slate-700' : 'text-white';
    $spotlightMediaClasses = $spotlightUsesContain
        ? 'h-full min-h-[18rem] w-full object-contain p-10 sm:p-14'
        : 'h-full min-h-[18rem] w-full object-cover';
    $heroImage = match ($page['page_key']) {
        'zoho_marketing_plus' => 'media/MarketingTeam.webp',
        'zoho_workplace' => 'media/WorkspaceTeam.webp',
        default => 'media/OpsTeam.webp',
    };
@endphp

@push('head')
    <link rel="preload" as="image" href="{{ asset($heroImage) }}" fetchpriority="high">
@endpush

@section('content')
    <x-marketing.hero
        :eyebrow="$content['eyebrow']"
        :headline="$content['headline']"
        :subheadline="$content['subheadline']"
        :primary-action="$heroAction"
        :secondary-action="$heroSecondaryAction"
        :hero-image="$heroImage"
    >
        <div id="consultation" class="relative">
            <div class="absolute inset-0 rounded-4xl bg-linear-to-br from-teal-300/14 via-transparent to-cyan-400/10 blur-2xl"></div>
            <div class="relative z-10">
                <livewire:marketing.product-lead :page-key="$page['page_key']" :product-name="$content['nav_label']" :trial-url="$page['trial_url']" />
            </div>
        </div>
    </x-marketing.hero>

    <x-marketing.trust-block
        :eyebrow="'Why teams choose '.$content['nav_label']"
        :title="data_get($content, 'trust_heading')"
        :copy="data_get($content, 'trust_copy')"
        :items="data_get($content, 'trust_blocks', [])"
        intro-class="max-w-3xl lg:max-w-[40rem] xl:max-w-[44rem]"
        items-class="mt-10 grid max-w-none gap-4 md:grid-cols-3 lg:max-w-[40rem] lg:grid-cols-2 xl:max-w-[44rem]"
        section-class="pb-12 pt-12 sm:pb-16 sm:pt-14 lg:pt-20"
        :span-last-odd-item="true"
    />

    <section class="pb-16 sm:pb-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-4xl border border-slate-200/80 bg-linear-to-br from-white/95 via-white/92 to-teal-50/70 px-6 py-6 shadow-[0_28px_70px_rgba(15,23,42,0.08)] sm:px-8 sm:py-8">
                <div class="pointer-events-none absolute inset-x-0 top-0 h-40 bg-[radial-gradient(circle_at_top_left,rgba(61,224,191,0.12),transparent_60%),radial-gradient(circle_at_top_right,rgba(56,189,248,0.09),transparent_55%)]"></div>
                <div class="relative grid gap-8 lg:grid-cols-[1.05fr_0.95fr]">
                    <div class="panel panel-strong p-8" data-reveal>
                        <p class="eyebrow">Best Fit</p>
                        <h2 class="section-title mt-4 text-balance">{{ $content['offer_title'] }}</h2>
                        <p class="section-copy mt-4">{{ $content['offer_copy'] }}</p>
                        @if(data_get($content, 'highlights', []) !== [])
                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                @foreach(data_get($content, 'highlights', []) as $highlight)
                                    <div class="flex items-start gap-3 rounded-2xl border border-slate-200/80 bg-white/90 px-4 py-3 text-sm font-medium text-slate-700">
                                        <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-linear-to-br from-teal-400 to-cyan-400"></span>
                                        <span>{{ $highlight }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="{{ $spotlightFrameClasses }}" data-reveal style="transition-delay: 120ms">
                        <div class="{{ $spotlightBadgeClasses }}">
                            @if(is_array($spotlightLogo))
                                <img src="{{ $spotlightLogo['src'] }}" alt="{{ $spotlightLogo['alt'] }}" class="h-6 w-auto object-contain">
                            @endif
                            <span class="text-[0.65rem] font-semibold uppercase tracking-[0.28em] {{ $spotlightBadgeTextClasses }}">
                                {{ $content['nav_label'] }}
                            </span>
                        </div>
                        @if(data_get($content, 'spotlight.media_type') === 'video')
                            <video class="{{ $spotlightMediaClasses }}" data-deferred-media="video" data-src="{{ asset(data_get($content, 'spotlight.media_url')) }}" controls playsinline muted preload="none"></video>
                        @else
                            <img src="{{ asset(data_get($content, 'spotlight.media_url')) }}" alt="{{ data_get($content, 'spotlight.media_alt', $content['nav_label']) }}" loading="lazy" decoding="async" class="{{ $spotlightMediaClasses }}">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-marketing.logo-bank
        eyebrow="Where this product wins"
        :title="data_get($content, 'logo_bank.title')"
        :copy="data_get($content, 'logo_bank.copy')"
        :items="data_get($content, 'logo_bank.items', [])"
    />

    <x-marketing.outcome-grid
        eyebrow="What improves after the move"
        :title="data_get($content, 'outcome_heading')"
        :copy="data_get($content, 'outcome_copy')"
        :items="data_get($content, 'outcomes', [])"
    />

    <x-marketing.feature-grid
        :title="$content['feature_heading']"
        :subtitle="$content['feature_subheading']"
        :features="$content['features']"
        :columns="4"
    />

    <x-marketing.faq-list
        :items="$content['faq']"
        :title="data_get($content, 'faq_heading', 'Questions buyers usually need answered before they convert.')"
        :copy="data_get($content, 'faq_copy', 'The goal is clarity and confidence, not over-explaining the platform.')"
    />

    <x-marketing.cta-band
        :headline="$content['cta_heading']"
        :copy="$content['cta_copy']"
        :primary="$heroAction"
        :secondary="['label' => 'Explore Another Product', 'url' => route('home') . '#qualify']"
    />
@endsection
