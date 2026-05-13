@props([
    'eyebrow' => 'Platform Coverage',
    'title' => null,
    'copy' => null,
    'items' => [],
])

@if($items !== [])
    <section class="pb-16 sm:pb-24">
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
                    @php
                        $usesContainMedia = data_get($item, 'media_type') !== 'video' && data_get($item, 'media_fit') === 'contain';
                        $mediaFrameClasses = $usesContainMedia
                            ? 'overflow-hidden rounded-[1.5rem] border border-teal-100/80 bg-linear-to-br from-white via-white to-teal-50 p-6 sm:p-8'
                            : 'overflow-hidden rounded-[1.5rem] border border-slate-200/80 bg-slate-950/5';
                        $mediaClasses = $usesContainMedia ? 'h-52 w-full object-contain' : 'h-52 w-full object-cover';
                        $logoLabel = str_contains(strtolower((string) ($item['alt'] ?? '')), 'partner') ? 'Partner support' : 'Zoho product';
                        $cardMediaUrl = match ((string) data_get($item, 'media_url')) {
                            'media/zoho-one-hero.jpg' => 'media/zoho-one-hero-card.jpg',
                            default => (string) data_get($item, 'media_url'),
                        };
                    @endphp
                    <article class="panel panel-strong flex h-full flex-col gap-5 overflow-hidden p-5 sm:p-6" data-reveal style="transition-delay: {{ $index * 90 }}ms">
                        @if(! empty($item['media_url']))
                            <div class="{{ $mediaFrameClasses }}">
                                @if(data_get($item, 'media_type') === 'video')
                                    <video
                                        class="h-52 w-full object-cover"
                                        data-deferred-media="video"
                                        data-src="{{ asset($item['media_url']) }}"
                                        aria-label="{{ data_get($item, 'media_alt', $item['title']) }}"
                                        autoplay
                                        loop
                                        muted
                                        playsinline
                                        preload="none"
                                    ></video>
                                @else
                                    <img src="{{ asset($cardMediaUrl) }}" alt="{{ data_get($item, 'media_alt', $item['title']) }}" loading="lazy" decoding="async" class="{{ $mediaClasses }}">
                                @endif
                            </div>
                        @endif
                        <div class="flex items-start gap-4 rounded-[1.25rem] border border-slate-200/80 bg-slate-50/90 px-4 py-4">
                            <div class="flex min-h-14 min-w-[7.5rem] items-center justify-center rounded-[1rem] border border-white bg-white px-4 py-3 shadow-[0_12px_24px_rgba(15,23,42,0.06)]">
                                <img src="{{ $item['src'] }}" alt="{{ $item['alt'] }}" loading="lazy" decoding="async" class="max-h-8 max-w-[8.5rem] w-auto object-contain">
                            </div>
                            <div class="space-y-2">
                                <p class="text-[0.7rem] font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $logoLabel }}</p>
                                <h3 class="text-base font-semibold text-slate-950">{{ $item['title'] }}</h3>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm leading-7 text-slate-600">{{ $item['description'] }}</p>
                        </div>
                        @if(! empty($item['url']))
                            <a href="{{ $item['url'] }}" class="mt-auto inline-flex items-center gap-2 text-sm font-semibold text-teal-700 transition hover:text-teal-800">
                                Explore this path
                                <span aria-hidden="true">→</span>
                            </a>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
