@php
    $products = \App\Support\PageCatalog::productNavigation();
@endphp

<footer class="border-t border-slate-200/70 bg-white/70 backdrop-blur">
    <div class="mx-auto grid max-w-7xl gap-10 px-6 py-14 lg:grid-cols-[1.2fr_0.8fr_0.6fr] lg:px-8">
        <div class="space-y-5">
            <div class="flex items-center">
                <img src="{{ asset('brand/infx-logo-wide.webp') }}" alt="INFX Solutions" class="h-12 w-auto sm:h-21">
            </div>
            <p class="max-w-xl text-sm leading-7 text-slate-600">
                Clear Zoho guidance, practical support, and local expertise to help your team move forward with confidence.
            </p>
            <div class="flex items-center">
                <img src="{{ asset('brand/zoho-authorized-partner.webp') }}" alt="Zoho Authorized Partner" class="h-11 w-auto sm:h-14">
            </div>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.26em] text-slate-400">Solutions</p>
            <ul class="mt-5 space-y-3">
                @foreach($products as $product)
                    <li>
                        <a href="{{ $product['slug'] }}" class="text-sm text-slate-600 transition hover:text-slate-950">{{ $product['label'] }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.26em] text-slate-400">Legal</p>
            <ul class="mt-5 space-y-3">
                <li><a href="{{ route('privacy') }}" class="text-sm text-slate-600 transition hover:text-slate-950">Privacy Policy</a></li>
                <li><a href="{{ route('terms') }}" class="text-sm text-slate-600 transition hover:text-slate-950">Terms of Service</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-slate-200/70">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-6 py-5 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between lg:px-8">
            <p>© {{ now()->year }} INFX Solutions (Pty) Ltd. All rights reserved.</p>
            <p>Zoho® is a trademark of Zoho Corporation Pvt. Ltd.</p>
        </div>
    </div>
</footer>
