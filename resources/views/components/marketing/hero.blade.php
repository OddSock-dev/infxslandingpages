@props([
    'eyebrow' => null,
    'headline',
    'subheadline',
    'primaryAction' => null,
    'secondaryAction' => null,
])

@php
    $hasAside = $slot->hasActualContent();
@endphp

<section data-hero-section class="relative overflow-hidden bg-slate-950 pb-8 pt-20 sm:pb-12 sm:pt-24 lg:pt-[6.5rem]">
    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(115deg,rgba(15,23,42,0.98)_0%,rgba(15,23,42,0.97)_45%,rgba(15,23,42,0.94)_72%,rgba(17,94,89,0.62)_100%)]"></div>
    <div class="pointer-events-none absolute inset-y-0 left-0 w-[58%] bg-[radial-gradient(circle_at_top_left,rgba(59,130,246,0.08),transparent_44%)]"></div>
    <div class="pointer-events-none absolute left-1/2 top-[-7rem] h-[28rem] w-[28rem] -translate-x-1/2 rounded-full bg-[radial-gradient(circle,rgba(61,224,191,0.08),transparent_72%)] blur-3xl"></div>
    <div class="pointer-events-none absolute right-[-10rem] top-8 h-[20rem] w-[20rem] rounded-full bg-[radial-gradient(circle,rgba(56,189,248,0.06),transparent_74%)] blur-3xl"></div>

    <div
        @class([
            'relative mx-auto px-6 sm:pt-6 lg:px-8',
            'grid max-w-7xl gap-6 pb-0 pt-4 lg:grid-cols-[minmax(0,1.1fr)_minmax(22rem,0.9fr)] lg:items-start lg:pt-8 xl:grid-cols-[minmax(0,1.06fr)_minmax(24rem,0.84fr)] xl:gap-10' => $hasAside,
            'max-w-7xl pb-14 pt-6 sm:pb-16 lg:pb-20 lg:pt-6' => ! $hasAside,
        ])
    >
        <div @class([
            'space-y-5 text-white sm:space-y-6',
            'lg:pr-6 lg:pt-4' => $hasAside,
            'max-w-[42rem] sm:max-w-[46rem] lg:max-w-[48rem]' => ! $hasAside,
        ])>
            @if($eyebrow)
                <p class="inline-flex items-center rounded-full border border-white/10 bg-slate-950/35 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-white/88 shadow-[0_12px_30px_rgba(15,23,42,0.18)] backdrop-blur" data-reveal>
                    {{ $eyebrow }}
                </p>
            @endif

            <div @class([
                'space-y-4 sm:space-y-5',
                'max-w-[42rem] sm:max-w-[44rem] lg:max-w-[40rem] xl:max-w-[43rem]' => $hasAside,
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
            <div data-reveal class="lg:pt-2" style="transition-delay: 160ms">
                {{ $slot }}
            </div>
        @endif
    </div>
</section>
