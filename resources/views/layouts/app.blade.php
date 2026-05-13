@php
    /** @var array<string, mixed> $page */
    $page = $page ?? [];
    $minimal = $minimal ?? false;
    $shouldLoadVite = ! app()->runningUnitTests()
        && (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')));
@endphp
<!DOCTYPE html>
<html lang="en-ZA" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $page['seo_title'] ?? config('app.name') }}</title>
        <meta name="description" content="{{ $page['meta_description'] ?? '' }}">
        <meta name="robots" content="{{ $page['robots'] ?? 'index, follow' }}">
        <meta property="og:site_name" content="INFX Solutions">
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $page['og_title'] ?? ($page['seo_title'] ?? config('app.name')) }}">
        <meta property="og:description" content="{{ $page['og_description'] ?? ($page['meta_description'] ?? '') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        @if(! empty($page['og_image_url']))
            <meta property="og:image" content="{{ $page['og_image_url'] }}">
        @endif
        <link rel="canonical" href="{{ url()->current() }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <meta name="theme-color" content="#ffffff">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">

        @if($shouldLoadVite)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @if(config('turnstile.enabled') && filled(config('turnstile.site_key')))
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" async defer></script>
        @endif
        @livewireStyles

        <!-- Google Analytics GA4 -->
        <x-analytics.ga4-script />
    </head>
    <body class="bg-canvas font-sans text-slate-900 antialiased">
        <div class="relative isolate min-h-screen overflow-x-hidden">
            <div class="pointer-events-none absolute inset-x-0 top-[-12rem] -z-10 h-[32rem] bg-[radial-gradient(circle_at_top,rgba(48,208,192,0.18),transparent_45%)]"></div>
            <div class="pointer-events-none absolute right-0 top-0 -z-10 h-[26rem] w-[26rem] rounded-full bg-[radial-gradient(circle,rgba(61,224,191,0.22),transparent_68%)] blur-3xl"></div>

            @unless($minimal)
                <x-marketing.header />
            @endunless

            <main>
                @yield('content')
            </main>

            @unless($minimal)
                <x-marketing.footer />
            @endunless
        </div>

        @livewireScripts
    </body>
</html>
