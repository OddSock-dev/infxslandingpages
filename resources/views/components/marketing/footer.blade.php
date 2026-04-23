@php
    $products = \App\Support\PageCatalog::productNavigation();
@endphp

<footer class="border-t border-slate-200/70 bg-white/70 backdrop-blur">
    <div class="mx-auto grid max-w-7xl gap-10 px-6 py-14 lg:grid-cols-[1.2fr_0.8fr_0.6fr] lg:px-8">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-linear-to-br from-orange-500 to-amber-500 text-sm font-bold text-white shadow-soft">IX</div>
                <div>
                    <p class="font-display text-base font-semibold tracking-tight text-slate-950">INFX Solutions</p>
                    <p class="text-sm text-slate-500">Zoho Authorised Partner</p>
                </div>
            </div>
            <p class="max-w-xl text-sm leading-7 text-slate-600">
                High-intent landing journeys, better lead handoff, and implementation support that keeps momentum going after the form fill.
            </p>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1 text-xs font-medium text-orange-700">SA-Based Delivery</span>
                <span class="inline-flex items-center rounded-full bg-teal-50 px-3 py-1 text-xs font-medium text-teal-700">POPIA-Conscious</span>
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">Zoho Rollouts</span>
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
