@php
    $products = \App\Support\PageCatalog::productNavigation();
    $socialLinks = [
        [
            'label' => 'Facebook',
            'href' => 'https://www.facebook.com/INFXSolutions',
            'icon' => 'facebook',
            'class' => 'text-[#1877F2]',
        ],
        [
            'label' => 'LinkedIn',
            'href' => 'https://www.linkedin.com/company/107888784',
            'icon' => 'linkedin',
            'class' => 'text-[#0A66C2]',
        ],
        [
            'label' => 'TikTok',
            'href' => 'https://www.tiktok.com/@infxsolutions',
            'icon' => 'tiktok',
            'class' => 'text-[#111111]',
        ],
        [
            'label' => 'YouTube',
            'href' => 'https://www.youtube.com/channel/UC1ef4HIZBmH1q5_ZOnaQSfQ',
            'icon' => 'youtube',
            'class' => 'text-[#FF0000]',
        ],
        [
            'label' => 'Instagram',
            'href' => 'https://www.instagram.com/infxsolutions',
            'icon' => 'instagram',
            'class' => 'text-[#E4405F]',
        ],
    ];
@endphp

<footer class="border-t border-slate-200/70 bg-white/70 backdrop-blur">
    <div class="mx-auto flex max-w-7xl flex-col gap-10 px-6 py-14 lg:flex-row lg:gap-16 lg:px-8">
        <div class="max-w-xl space-y-6 lg:w-[24rem] lg:flex-none">
            <div class="flex items-center">
                <img src="{{ asset('brand/infx-logo-wide.webp') }}" alt="INFX Solutions" loading="lazy" decoding="async" class="h-12 w-auto sm:h-21">
            </div>
            <p class="max-w-xl text-sm leading-7 text-slate-600">
                Clear Zoho guidance, practical support, and local expertise<br>to help your team move forward with confidence.
            </p>
        </div>

        <div class="grid flex-1 gap-10 sm:grid-cols-2 xl:grid-cols-3">
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
                <p class="text-xs font-semibold uppercase tracking-[0.26em] text-slate-400">Contact</p>
                <ul class="mt-5 space-y-4 text-sm leading-7 text-slate-600">
                    <li>
                        <span class="font-medium text-slate-950">Address:</span>
                        <span class="mt-1 block">21, Eastwood Office Park, 11B Riley Rd, Bedfordview, Germiston, 2007</span>
                    </li>
                    <li>
                        <span class="font-medium text-slate-950">Phone:</span>
                        <a href="tel:+27100207721" class="ml-1 transition hover:text-slate-950">010 020 7721</a>
                    </li>
                    <li>
                        <span class="font-medium text-slate-950">Email:</span>
                        <a href="mailto:info@infxsolutions.co.za" class="ml-1 transition hover:text-slate-950">info@infxsolutions.co.za</a>
                    </li>
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
    </div>

    <div class="border-t border-slate-200/70">
        <div class="mx-auto flex max-w-7xl flex-col gap-5 px-6 py-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div class="flex items-center">
                <img src="{{ asset('brand/zoho-authorized-partner.webp') }}" alt="Zoho Authorized Partner" loading="lazy" decoding="async" class="h-11 w-auto shrink-0 sm:h-14">
            </div>

            <div class="flex flex-wrap items-center gap-3 lg:justify-end">
                @foreach($socialLinks as $socialLink)
                    <a
                        href="{{ $socialLink['href'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="{{ $socialLink['label'] }}"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white shadow-xs transition hover:-translate-y-0.5 hover:border-slate-300 hover:shadow-sm"
                    >
                        <x-marketing.icon :name="$socialLink['icon']" class="h-4 w-4 {{ $socialLink['class'] }}" />
                        <span class="sr-only">{{ $socialLink['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="border-t border-slate-200/70">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-6 py-5 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between lg:px-8">
            <p>© {{ now()->year }} INFX Solutions (Pty) Ltd. All rights reserved.</p>
            <p>Zoho® is a trademark of Zoho Corporation Pvt. Ltd.</p>
        </div>
    </div>
</footer>
