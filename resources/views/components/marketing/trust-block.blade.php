@props([
    'eyebrow' => 'Why INFX',
    'title' => null,
    'copy' => null,
    'items' => [],
    'introClass' => 'max-w-3xl',
    'itemsClass' => 'mt-10 grid gap-4 md:grid-cols-3',
    'sectionClass' => 'pb-12 sm:pb-16',
    'spanLastOddItem' => false,
])

@if($items !== [])
    <section class="{{ $sectionClass }}">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="{{ $introClass }}" data-reveal>
                <p class="eyebrow">{{ $eyebrow }}</p>
                @if($title)
                    <h2 class="section-title mt-4 text-balance">{{ $title }}</h2>
                @endif
                @if($copy)
                    <p class="section-copy mt-4">{{ $copy }}</p>
                @endif
            </div>

            <div class="{{ $itemsClass }}">
                @foreach($items as $index => $item)
                    <article @class([
                        'panel panel-strong h-full px-6 py-6 sm:px-7',
                        'md:col-span-2' => $spanLastOddItem && $loop->last && $loop->count % 2 === 1,
                    ]) data-reveal style="transition-delay: {{ $index * 80 }}ms">
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
