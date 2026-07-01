@extends('layouts.app')

@section('content')
<div class="mx-auto {{ ($page->content_type ?? 'text') === 'html' ? 'max-w-5xl' : 'max-w-3xl' }} px-4 pb-16 sm:px-6 lg:px-8">
    {{-- Breadcrumb navigation --}}
    <nav class="mb-6 flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition-colors">Home</a>
        <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="text-slate-700 font-medium">{{ $page->title }}</span>
    </nav>

    <div class="card p-6 sm:p-10">
        <header class="border-b border-slate-100 pb-6 mb-8">
            <h1 class="font-display text-3xl sm:text-4xl font-extrabold tracking-tight text-ink-900">{{ $page->title }}</h1>
            @if($page->updated_at)
                <p class="mt-2 text-sm text-slate-400 flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Last updated {{ $page->updated_at->format('F j, Y') }}
                </p>
            @endif
        </header>

        @if(($page->content_type ?? 'text') === 'html')
            {{-- Raw HTML content rendered as-is --}}
            <div class="page-html-content">
                {!! $page->content !!}
            </div>
        @else
            {{-- Plain text with nl2br rendering --}}
            <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed whitespace-pre-line">
                {!! nl2br(e($page->content)) !!}
            </div>
        @endif
    </div>
</div>
@endsection
