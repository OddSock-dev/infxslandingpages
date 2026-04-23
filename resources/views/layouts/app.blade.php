@php
    /** @var array<string, mixed> $page */
    $page = $page ?? [];
    $minimal = $minimal ?? false;
    $hasViteManifest = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
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
        <link rel="icon" href="/favicon.ico">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">

        @if($hasViteManifest)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @livewireStyles
    </head>
    <body class="bg-canvas font-sans text-slate-900 antialiased">
        <div class="relative isolate min-h-screen overflow-x-hidden">
            <div class="pointer-events-none absolute inset-x-0 top-[-12rem] -z-10 h-[32rem] bg-[radial-gradient(circle_at_top,rgba(255,159,67,0.20),transparent_45%)]"></div>
            <div class="pointer-events-none absolute right-0 top-0 -z-10 h-[26rem] w-[26rem] rounded-full bg-[radial-gradient(circle,rgba(61,224,191,0.22),transparent_68%)] blur-3xl"></div>

            @unless($minimal)
                <x-marketing.header />
            @endunless

            <main class="{{ $minimal ? '' : 'pt-24 sm:pt-28' }}">
                @yield('content')
            </main>

            @unless($minimal)
                <x-marketing.footer />
            @endunless
        </div>

        @livewireScripts
    </body>
</html>
