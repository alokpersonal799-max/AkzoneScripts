@extends('layouts.app')

@section('content')
@php
    $layout = $page->layout ?? 'card';
    $width = $layout === 'wide' ? 'max-w-5xl' : 'max-w-3xl';
@endphp

{{-- Typography for page content (Tailwind CDN has no typography plugin, so we
     style bare tags here). :where() keeps specificity 0 so any inline utility
     classes in HTML pages always win. --}}
<style>
    :where(.page-body) h1{font-size:2rem;font-weight:800;color:#0b1120;margin:1.5rem 0 .75rem;line-height:1.2}
    :where(.page-body) h2{font-size:1.5rem;font-weight:700;color:#0b1120;margin:1.75rem 0 .75rem}
    :where(.page-body) h3{font-size:1.25rem;font-weight:700;color:#1b2540;margin:1.4rem 0 .5rem}
    :where(.page-body) h4{font-size:1.05rem;font-weight:600;color:#1b2540;margin:1.1rem 0 .5rem}
    :where(.page-body) p{margin:.85rem 0;line-height:1.75;color:#475569}
    :where(.page-body) ul{list-style:disc;padding-left:1.5rem;margin:.85rem 0;color:#475569}
    :where(.page-body) ol{list-style:decimal;padding-left:1.5rem;margin:.85rem 0;color:#475569}
    :where(.page-body) li{margin:.35rem 0;line-height:1.7}
    :where(.page-body) a{color:#2563eb;text-decoration:underline}
    :where(.page-body) strong{color:#0b1120;font-weight:700}
    :where(.page-body) blockquote{border-left:4px solid #e2e8f0;padding-left:1rem;margin:1rem 0;color:#64748b;font-style:italic}
    :where(.page-body) hr{border:0;border-top:1px solid #e2e8f0;margin:1.75rem 0}
    :where(.page-body) section{margin-bottom:1.5rem}
    :where(.page-body) img{border-radius:.75rem;max-width:100%;height:auto}
    :where(.page-body) table{width:100%;border-collapse:collapse;margin:1rem 0;font-size:.9rem}
    :where(.page-body) th,:where(.page-body) td{border:1px solid #e2e8f0;padding:.55rem .75rem;text-align:left}
    :where(.page-body) th{background:#f8fafc;font-weight:600;color:#0b1120}
</style>

<div class="mx-auto {{ $width }} px-4 pb-16 pt-8 sm:px-6 lg:px-8">
    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center gap-2 text-sm text-slate-500">
        <a href="{{ route('home') }}" class="transition-colors hover:text-brand-600">Home</a>
        <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-slate-700">{{ $page->title }}</span>
    </nav>

    <div class="{{ $layout === 'plain' ? '' : 'card p-6 sm:p-10' }}">
        <header class="mb-8 border-b border-slate-100 pb-6">
            <h1 class="font-display text-3xl font-extrabold tracking-tight text-ink-900 sm:text-4xl">{{ $page->title }}</h1>
            @if ($page->updated_at)
                <p class="mt-2 flex items-center gap-1.5 text-sm text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Last updated {{ $page->updated_at->format('F j, Y') }}
                </p>
            @endif
        </header>

        <div class="page-body">
            @if (($page->content_type ?? 'text') === 'html')
                {!! $page->content !!}
            @else
                <div class="whitespace-pre-line">{!! nl2br(e($page->content)) !!}</div>
            @endif
        </div>
    </div>

    @include('partials.ads', ['page' => 'pages'])
</div>
@endsection
