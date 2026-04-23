@props([
    'eyebrow' => null,
    'headline',
    'subheadline',
    'highlights' => [],
    'stats' => [],
    'primaryAction' => null,
    'secondaryAction' => null,
])

<section class="relative overflow-hidden pb-16 sm:pb-20">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[42rem] bg-[radial-gradient(circle_at_top_left,rgba(11,19,43,0.96),rgba(15,23,42,0.92)_42%,rgba(15,23,42,0.86)_64%,transparent_85%)]"></div>
    <div class="pointer-events-none absolute left-1/2 top-[-6rem] h-[30rem] w-[30rem] -translate-x-1/2 rounded-full bg-[radial-gradient(circle,rgba(61,224,191,0.16),transparent_70%)] blur-3xl"></div>
    <div class="pointer-events-none absolute right-[-8rem] top-8 h-[22rem] w-[22rem] rounded-full bg-[radial-gradient(circle,rgba(255,159,67,0.22),transparent_70%)] blur-3xl"></div>

    <div class="relative mx-auto grid max-w-7xl gap-12 px-6 pb-4 pt-10 lg:grid-cols-[1.08fr_0.92fr] lg:items-center lg:px-8">
        <div class="space-y-8 text-white">
            @if($eyebrow)
                <p class="inline-flex items-center rounded-full border border-white/15 bg-white/8 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-orange-200" data-reveal>
                    {{ $eyebrow }}
                </p>
            @endif

            <div class="max-w-3xl space-y-5">
                <h1 class="font-display text-4xl font-semibold leading-[1.02] tracking-tight text-balance sm:text-5xl lg:text-7xl" data-reveal>
                    {{ $headline }}
                </h1>
                <p class="max-w-2xl text-base leading-8 text-slate-300 sm:text-lg" data-reveal style="transition-delay: 90ms">
                    {{ $subheadline }}
                </p>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row" data-reveal style="transition-delay: 160ms">
                @if($primaryAction)
                    <a href="{{ $primaryAction['url'] }}" class="inline-flex items-center justify-center rounded-full bg-linear-to-r from-orange-500 to-amber-500 px-6 py-3.5 text-sm font-semibold text-white shadow-soft transition hover:-translate-y-0.5">
                        {{ $primaryAction['label'] }}
                    </a>
                @endif
                @if($secondaryAction)
                    <a href="{{ $secondaryAction['url'] }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/8 px-6 py-3.5 text-sm font-semibold text-white transition hover:border-white/25 hover:bg-white/12">
                        {{ $secondaryAction['label'] }}
                    </a>
                @endif
            </div>

            @if($highlights !== [])
                <div class="grid gap-3 sm:grid-cols-3" data-reveal style="transition-delay: 220ms">
                    @foreach($highlights as $highlight)
                        <div class="rounded-3xl border border-white/10 bg-white/7 px-4 py-4 text-sm leading-6 text-slate-200 backdrop-blur">
                            {{ $highlight }}
                        </div>
                    @endforeach
                </div>
            @endif

            @if($stats !== [])
                <div class="grid gap-4 sm:grid-cols-4" data-reveal style="transition-delay: 300ms">
                    @foreach($stats as $stat)
                        <div class="rounded-3xl border border-white/10 bg-slate-950/35 px-4 py-4 backdrop-blur">
                            <div class="font-display text-3xl font-semibold text-white">{{ $stat['value'] }}</div>
                            <div class="mt-2 text-sm text-slate-300">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div data-reveal style="transition-delay: 160ms">
            {{ $slot }}
        </div>
    </div>
</section>
