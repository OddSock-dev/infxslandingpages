@props(['items' => []])

<section class="pb-16 sm:pb-24">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="flex items-end justify-between gap-6" data-reveal>
            <div class="max-w-2xl">
                <p class="eyebrow">Client Signal</p>
                <h2 class="section-title mt-4">The experience is designed to feel consultative from the first click.</h2>
            </div>
        </div>

        <div class="mt-10 grid gap-5 lg:grid-cols-3">
            @foreach($items as $index => $item)
                <blockquote class="panel h-full p-6 sm:p-7" data-reveal style="transition-delay: {{ $index * 100 }}ms">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M7.17 6A4.17 4.17 0 003 10.17V18h6v-7H6.83A2.17 2.17 0 019 8.83V6H7.17zm9 0A4.17 4.17 0 0012 10.17V18h6v-7h-2.17A2.17 2.17 0 0118 8.83V6h-1.83z" />
                        </svg>
                    </div>
                    <p class="mt-5 text-base leading-8 text-slate-700">{{ $item['quote'] }}</p>
                    <footer class="mt-6">
                        <div class="font-semibold text-slate-950">{{ $item['author'] }}</div>
                        <div class="text-sm text-slate-500">{{ $item['role'] }}</div>
                    </footer>
                </blockquote>
            @endforeach
        </div>
    </div>
</section>
