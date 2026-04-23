@extends('layouts.app', ['page' => $page, 'minimal' => true])

@section('content')
    <section class="flex min-h-screen items-center justify-center px-6 py-16">
        <div class="panel mx-auto max-w-2xl p-8 text-center sm:p-12" data-reveal>
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-linear-to-br from-teal-400 to-cyan-500 text-white shadow-soft">
                <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
            </div>
            <p class="eyebrow mt-8">Request Received</p>
            <h1 class="mt-4 font-display text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">{{ $page['body_config']['headline'] }}</h1>
            <p class="mx-auto mt-4 max-w-xl text-base leading-8 text-slate-600">{{ $page['body_config']['subheadline'] }}</p>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white shadow-soft transition hover:-translate-y-0.5">
                    Back To The Hub
                </a>
                <a href="https://www.zoho.com/blog" target="_blank" rel="noreferrer noopener" class="inline-flex items-center justify-center rounded-full border border-slate-200 px-6 py-3.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                    Explore Zoho Insights
                </a>
            </div>
        </div>
    </section>
@endsection
