@props(['name' => 'spark'])

@switch($name)
    @case('spark')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3z" />
        </svg>
        @break
    @case('compass')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="8.25" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.8 9.2l-2 5.6-5.6 2 2-5.6 5.6-2z" />
        </svg>
        @break
    @case('shield')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 3.75V12c0 4.2-2.7 7.95-7 9-4.3-1.05-7-4.8-7-9V6.75L12 3z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 12l1.7 1.7L14.8 10" />
        </svg>
        @break
    @case('bolt')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 2L4 14h6l-1 8 9-12h-6l1-8z" />
        </svg>
        @break
    @case('layers')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4l8 4-8 4-8-4 8-4z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 12l8 4 8-4" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l8 4 8-4" />
        </svg>
        @break
    @case('chart')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V9" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16v-6" />
        </svg>
        @break
    @case('gear')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.5A3.5 3.5 0 1112 15.5 3.5 3.5 0 0112 8.5z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.4 15a1 1 0 00.2 1.1l.1.1a2 2 0 01-2.8 2.8l-.1-.1a1 1 0 00-1.1-.2 1 1 0 00-.6.9V21a2 2 0 01-4 0v-.2a1 1 0 00-.6-.9 1 1 0 00-1.1.2l-.1.1a2 2 0 11-2.8-2.8l.1-.1a1 1 0 00.2-1.1 1 1 0 00-.9-.6H3a2 2 0 010-4h.2a1 1 0 00.9-.6 1 1 0 00-.2-1.1l-.1-.1a2 2 0 112.8-2.8l.1.1a1 1 0 001.1.2 1 1 0 00.6-.9V3a2 2 0 014 0v.2a1 1 0 00.6.9 1 1 0 001.1-.2l.1-.1a2 2 0 112.8 2.8l-.1.1a1 1 0 00-.2 1.1 1 1 0 00.9.6H21a2 2 0 010 4h-.2a1 1 0 00-.9.6z" />
        </svg>
        @break
    @case('rocket')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.5 5.5c3.5.3 4 4 4 4s-2.8 4.8-7 7c-1.4.8-3 1.4-4.6 1.7.3-1.6.9-3.2 1.7-4.6 2.2-4.2 7-7 7-7z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15l-3 3" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 8l8 8" />
        </svg>
        @break
    @case('megaphone')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10v4a1 1 0 001 1h2l3 4h2v-4.5l6 2.5V7l-6 2.5V5h-2l-3 4H6a1 1 0 00-1 1z" />
        </svg>
        @break
    @case('route')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="6" cy="6" r="2.5" />
            <circle cx="18" cy="18" r="2.5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 6H12a4 4 0 014 4v5.5" />
        </svg>
        @break
    @case('pulse')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h4l2-4 4 8 2-4h6" />
        </svg>
        @break
    @case('mail')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <rect x="3.5" y="5" width="17" height="14" rx="2" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7l8 6 8-6" />
        </svg>
        @break
    @case('facebook')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 320 512" fill="currentColor">
            <path d="M80 299.3V512H196V299.3h86.5l18-97.8H196V166.9c0-51.7 20.3-71.5 72.7-71.5c16.3 0 29.4 .4 37 1.2V7.9C291.4 4 256.4 0 236.2 0C129.3 0 80 50.5 80 159.4v42.1H14v97.8H80z" />
        </svg>
        @break
    @case('linkedin')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 448 512" fill="currentColor">
            <path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z" />
        </svg>
        @break
    @case('tiktok')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 448 512" fill="currentColor">
            <path d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z" />
        </svg>
        @break
    @case('youtube')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 576 512" fill="currentColor">
            <path d="M549.655 124.083c-6.281-23.65-24.787-42.276-48.284-48.597C458.781 64 288 64 288 64S117.22 64 74.629 75.486c-23.497 6.322-42.003 24.947-48.284 48.597-11.412 42.867-11.412 132.305-11.412 132.305s0 89.438 11.412 132.305c6.281 23.65 24.787 41.5 48.284 47.821C117.22 448 288 448 288 448s170.78 0 213.371-11.486c23.497-6.321 42.003-24.171 48.284-47.821 11.412-42.867 11.412-132.305 11.412-132.305s0-89.438-11.412-132.305zm-317.51 213.508V175.185l142.739 81.205-142.739 81.201z" />
        </svg>
        @break
    @case('instagram')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 448 512" fill="currentColor">
            <path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z" />
        </svg>
        @break
    @case('document')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 3.5h6l4 4V20a1.5 1.5 0 01-1.5 1.5h-8A1.5 1.5 0 017 20V5A1.5 1.5 0 018.5 3.5z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 3.5V8h4" />
        </svg>
        @break
    @case('swap')
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h11m0 0l-3-3m3 3l-3 3" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17H6m0 0l3 3m-3-3l3-3" />
        </svg>
        @break
    @default
        <svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <circle cx="12" cy="12" r="8" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l2.5 2.5" />
        </svg>
@endswitch
