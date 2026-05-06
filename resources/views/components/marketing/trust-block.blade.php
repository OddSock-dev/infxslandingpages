@props([
    'eyebrow' => 'Why INFX',
    'title' => null,
    'copy' => null,
    'items' => [],
])

@if($items !== [])
    <section class="pb-12 sm:pb-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl" data-reveal>
                <p class="eyebrow">{{ $eyebrow }}</p>
                @if($title)
                    <h2 class="section-title mt-4 text-balance">{{ $title }}</h2>
                @endif
                @if($copy)
                    <p class="section-copy mt-4">{{ $copy }}</p>
                @endif
            </div>

            <div class="mt-10 grid gap-4 md:grid-cols-3">
                @foreach($items as $index => $item)
                    <article class="panel panel-strong h-full px-6 py-6 sm:px-7" data-reveal style="transition-delay: {{ $index * 80 }}ms">
                        <div class="flex items-start gap-4">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-slate-950 text-teal-300 shadow-soft">
                                <x-marketing.icon :name="$item['icon']" />
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-slate-950">{{ $item['title'] }}</h3>
                                <p class="mt-2 text-sm leading-7 text-slate-600">{{ $item['description'] }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
