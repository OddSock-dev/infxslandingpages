@php
    $products = \App\Support\PageCatalog::productNavigation();
@endphp

<header data-scroll-header class="site-header fixed inset-x-0 top-0 z-50">
    <div class="mx-auto max-w-6xl px-4 pt-4 sm:px-6 lg:px-8">
        <div class="site-header__panel rounded-[1.75rem] px-4 py-3 sm:px-5 sm:py-3.5">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="site-header__brand flex min-w-0 items-center">
                    <img src="{{ asset('brand/infx-logo-wide-header.png') }}" alt="INFX Solutions" width="200" height="136" decoding="async" class="site-header__brand-image h-11 w-auto sm:h-14">
                </a>

                <nav class="hidden items-center gap-6 lg:flex">
                    @foreach($products as $product)
                        <a href="{{ $product['slug'] }}" class="site-header__link text-sm font-medium transition">
                            {{ $product['label'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('privacy') }}" class="site-header__link text-sm font-medium transition">Privacy</a>
                </nav>

                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('home') }}#qualify"
                        class="site-header__cta hidden items-center rounded-full px-4 py-2.5 text-sm font-semibold transition hover:-translate-y-0.5 sm:inline-flex"
                    >
                        Start The Match
                    </a>
                    <button
                        type="button"
                        data-nav-toggle
                        aria-expanded="false"
                        class="site-header__menu-button inline-flex h-11 w-11 items-center justify-center rounded-2xl transition lg:hidden"
                    >
                        <span class="sr-only">Toggle navigation</span>
                        ☰
                    </button>
                </div>
            </div>

            <div data-mobile-nav data-open="false" class="site-header__mobile-panel hidden mt-4 border-t pt-4 lg:hidden">
                <nav class="grid gap-3">
                    @foreach($products as $product)
                        <a href="{{ $product['slug'] }}" class="site-header__mobile-link rounded-2xl px-3 py-2 text-sm font-medium transition">
                            {{ $product['label'] }}
                        </a>
                    @endforeach
                    <a href="{{ route('privacy') }}" class="site-header__mobile-link rounded-2xl px-3 py-2 text-sm font-medium transition">Privacy</a>
                    <a href="{{ route('home') }}#qualify" class="site-header__mobile-cta rounded-2xl px-3 py-2 text-sm font-semibold text-white">
                        Start The Match
                    </a>
                </nav>
            </div>
        </div>
    </div>
</header>
