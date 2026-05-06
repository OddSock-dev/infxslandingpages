@props([
    'title',
    'subtitle' => null,
    'features' => [],
    'columns' => 4,
])

<section class="pb-16 sm:pb-24">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="max-w-3xl" data-reveal>
            <p class="eyebrow">Why Teams Choose It</p>
            <h2 class="section-title mt-4 text-balance">{{ $title }}</h2>
            @if($subtitle)
                <p class="section-copy mt-4">{{ $subtitle }}</p>
            @endif
        </div>

        <div @class([
            'mt-10 grid gap-5',
            'md:grid-cols-2 xl:grid-cols-4' => $columns === 4,
            'md:grid-cols-2 xl:grid-cols-3' => $columns === 3,
            'md:grid-cols-2' => $columns === 2,
        ])>
            @foreach($features as $index => $feature)
                <article class="panel panel-strong h-full p-6 sm:p-7" data-reveal style="transition-delay: {{ $index * 90 }}ms">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-linear-to-br from-slate-950 to-slate-800 text-teal-300 shadow-soft">
                        <x-marketing.icon :name="$feature['icon']" />
                    </div>
                    <h3 class="mt-5 text-lg font-semibold text-slate-950">{{ $feature['title'] }}</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-600">{{ $feature['description'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
