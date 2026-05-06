@props([
    'eyebrow' => 'Outcomes',
    'title',
    'copy' => null,
    'items' => [],
])

@if($items !== [])
    <section class="pb-16 sm:pb-24">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl" data-reveal>
                <p class="eyebrow">{{ $eyebrow }}</p>
                <h2 class="section-title mt-4 text-balance">{{ $title }}</h2>
                @if($copy)
                    <p class="section-copy mt-4">{{ $copy }}</p>
                @endif
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                @foreach($items as $index => $item)
                    <article class="relative overflow-hidden rounded-[1.75rem] bg-slate-950 p-6 text-white shadow-hero" data-reveal style="transition-delay: {{ $index * 90 }}ms">
                        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(48,208,192,0.24),transparent_42%),radial-gradient(circle_at_bottom_right,rgba(56,189,248,0.22),transparent_44%)]"></div>
                        <div class="relative space-y-4">
                            <p class="text-4xl font-display font-semibold tracking-tight">{{ $item['metric'] }}</p>
                            <div>
                                <h3 class="text-lg font-semibold">{{ $item['title'] }}</h3>
                                <p class="mt-2 text-sm leading-7 text-slate-300">{{ $item['description'] }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
