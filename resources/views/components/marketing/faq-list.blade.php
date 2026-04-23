@props(['items' => []])

<section class="pb-16 sm:pb-24">
    <div class="mx-auto grid max-w-7xl gap-10 px-6 lg:grid-cols-[0.78fr_1.22fr] lg:px-8">
        <div data-reveal>
            <p class="eyebrow">FAQ</p>
            <h2 class="section-title mt-4">Questions buyers usually need answered before they convert.</h2>
            <p class="section-copy mt-4">The goal is clarity and confidence, not over-explaining the platform.</p>
        </div>

        <div class="space-y-4">
            @foreach($items as $index => $item)
                <details class="panel group p-0" data-reveal style="transition-delay: {{ $index * 80 }}ms">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-6 py-5 text-left">
                        <span class="text-base font-semibold text-slate-950">{{ $item['question'] }}</span>
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition group-open:rotate-45">+</span>
                    </summary>
                    <div class="px-6 pb-5 text-sm leading-7 text-slate-600">
                        {{ $item['answer'] }}
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>
