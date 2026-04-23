@php
    $products = \App\Support\PageCatalog::productNavigation();
@endphp

<header class="fixed inset-x-0 top-0 z-50">
    <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
        <div class="glass-panel flex items-center justify-between gap-4 rounded-full px-4 py-3 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-linear-to-br from-orange-500 to-amber-500 text-sm font-bold text-white shadow-soft">IX</div>
                <div>
                    <p class="font-display text-sm font-semibold tracking-tight text-slate-950">INFX Solutions</p>
                    <p class="text-xs text-slate-500">Zoho Growth Partner</p>
                </div>
            </a>

            <nav class="hidden items-center gap-6 lg:flex">
                @foreach($products as $product)
                    <a href="{{ $product['slug'] }}" class="text-sm font-medium text-slate-600 transition hover:text-slate-950">
                        {{ $product['label'] }}
                    </a>
                @endforeach
                <a href="{{ route('privacy') }}" class="text-sm font-medium text-slate-600 transition hover:text-slate-950">Privacy</a>
            </nav>

            <a
                href="{{ route('home') }}#qualify"
                class="inline-flex items-center rounded-full bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:-translate-y-0.5 hover:bg-slate-900"
            >
                Start The Match
            </a>
        </div>
    </div>
</header>
