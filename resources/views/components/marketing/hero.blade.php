@props([
    'eyebrow' => null,
    'headline',
    'subheadline',
    'primaryAction' => null,
    'secondaryAction' => null,
    'heroImage' => 'media/OpsTeam.webp',
])

@php
    $hasAside = $slot->hasActualContent();
@endphp

<section data-hero-section class="relative overflow-visible bg-slate-950 pb-8 pt-20 sm:pb-12 sm:pt-24 lg:pt-26">
    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(115deg,rgba(15,23,42,0.98)_0%,rgba(15,23,42,0.97)_45%,rgba(15,23,42,0.94)_72%,rgba(17,94,89,0.62)_100%)]"></div>
    <div class="pointer-events-none absolute inset-y-0 left-0 w-[58%] bg-[radial-gradient(circle_at_top_left,rgba(59,130,246,0.08),transparent_44%)]"></div>
    <div class="pointer-events-none absolute left-1/2 -top-28 h-112 w-md -translate-x-1/2 rounded-full bg-[radial-gradient(circle,rgba(61,224,191,0.08),transparent_72%)] blur-3xl"></div>
    <div class="pointer-events-none absolute -right-40 top-8 h-80 w-80 rounded-full bg-[radial-gradient(circle,rgba(56,189,248,0.06),transparent_74%)] blur-3xl"></div>

    @if($hasAside)
        <div class="pointer-events-none absolute inset-x-0 top-0 z-0 hidden h-full overflow-hidden lg:block">
            <img
                src="{{ asset($heroImage) }}"
                alt=""
                aria-hidden="true"
                fetchpriority="high"
                decoding="async"
                class="absolute left-[66rem] top-16 h-[calc(100%-4rem)] max-w-[58rem] object-contain object-bottom opacity-90 xl:left-[74rem] xl:top-14 xl:h-[calc(100%-3.5rem)]"
            >
        </div>
    @endif

    <div
        @class([
            'relative mx-auto px-6 sm:pt-6 lg:px-8',
            'grid max-w-7xl gap-8 pb-10 pt-4 lg:min-h-[34rem] lg:grid-cols-[minmax(0,0.96fr)_minmax(21rem,30rem)] lg:items-start lg:gap-5 lg:pb-20 lg:pt-8 xl:min-h-[36rem] xl:grid-cols-[minmax(0,0.94fr)_minmax(23rem,31rem)] xl:gap-8' => $hasAside,
            'max-w-7xl pb-14 pt-6 sm:pb-16 lg:pb-20 lg:pt-6' => ! $hasAside,
        ])
    >
        <div @class([
            'relative z-10 space-y-5 text-white sm:space-y-6',
            'lg:pt-4' => $hasAside,
            'max-w-[42rem] sm:max-w-[46rem] lg:max-w-[48rem]' => ! $hasAside,
        ])>
            @if($eyebrow)
                <p class="inline-flex items-center rounded-full border border-white/10 bg-slate-950/35 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-white/88 shadow-[0_12px_30px_rgba(15,23,42,0.18)] backdrop-blur" data-reveal>
                    {{ $eyebrow }}
                </p>
            @endif

            <div @class([
                'space-y-4 sm:space-y-5',
                'max-w-[42rem] sm:max-w-[44rem] lg:max-w-[38rem] xl:max-w-[41rem]' => $hasAside,
            ])>
                <h1
                    @class([
                        'hero-title font-display font-semibold tracking-tight text-white' => $hasAside,
                        'max-w-[15.5ch] font-display text-[clamp(2.25rem,4vw,3.95rem)] font-semibold leading-[0.95] tracking-[-0.055em] text-white text-balance sm:max-w-[17ch]' => ! $hasAside,
                    ])
                    data-reveal
                >
                    {{ $headline }}
                </h1>
                <p @class([
                    'hero-copy text-slate-100/92',
                    'max-w-xl' => $hasAside,
                    'max-w-[34rem] text-[1.08rem] leading-8 text-slate-100/90' => ! $hasAside,
                ]) data-reveal style="transition-delay: 90ms">
                    {{ $subheadline }}
                </p>
            </div>

            <div @class([
                'flex flex-col gap-4 sm:flex-row',
            ]) data-reveal style="transition-delay: 160ms">
                @if($primaryAction)
                    <a href="{{ $primaryAction['url'] }}" class="inline-flex items-center justify-center rounded-full bg-linear-to-r from-teal-500 to-cyan-500 px-6 py-3.5 text-sm font-semibold text-white shadow-soft transition hover:-translate-y-0.5">
                        {{ $primaryAction['label'] }}
                    </a>
                @endif
                @if($secondaryAction)
                    <a href="{{ $secondaryAction['url'] }}" class="inline-flex items-center justify-center rounded-full border border-white/12 bg-slate-950/30 px-6 py-3.5 text-sm font-semibold text-white transition hover:border-white/22 hover:bg-slate-950/42">
                        {{ $secondaryAction['label'] }}
                    </a>
                @endif
            </div>
        </div>

        @if($hasAside)
            <div data-reveal class="relative z-30 w-full lg:absolute lg:right-8 lg:top-112 lg:w-124 xl:top-116" style="transition-delay: 160ms">
                {{ $slot }}
            </div>
        @endif
    </div>
</section>
