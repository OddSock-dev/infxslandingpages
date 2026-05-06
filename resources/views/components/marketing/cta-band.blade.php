@props([
    'headline',
    'copy',
    'primary' => null,
    'secondary' => null,
])

<section class="pb-20 sm:pb-28">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-4xl bg-slate-950 px-7 py-10 text-white shadow-hero sm:px-10 sm:py-12" data-reveal>
            <div class="pointer-events-none absolute inset-y-0 right-0 w-1/2 bg-[radial-gradient(circle_at_right,rgba(61,224,191,0.24),transparent_62%)]"></div>
            <div class="pointer-events-none absolute inset-x-0 top-0 h-full bg-[radial-gradient(circle_at_top_left,rgba(56,189,248,0.22),transparent_42%)]"></div>

            <div class="relative flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="eyebrow text-teal-200/90!">Next Step</p>
                    <h2 class="mt-4 font-display text-3xl font-semibold tracking-tight text-balance sm:text-4xl">{{ $headline }}</h2>
                    <p class="mt-4 max-w-2xl text-base leading-8 text-slate-300">{{ $copy }}</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    @if($primary)
                        <a href="{{ $primary['url'] }}" class="inline-flex items-center justify-center rounded-full bg-linear-to-r from-teal-500 to-cyan-500 px-6 py-3.5 text-sm font-semibold text-white shadow-soft transition hover:-translate-y-0.5">
                            {{ $primary['label'] }}
                        </a>
                    @endif
                    @if($secondary)
                        <a href="{{ $secondary['url'] }}" class="inline-flex items-center justify-center rounded-full border border-white/15 bg-white/8 px-6 py-3.5 text-sm font-semibold text-white transition hover:border-white/25 hover:bg-white/12">
                            {{ $secondary['label'] }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
