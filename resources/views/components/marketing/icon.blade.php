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
