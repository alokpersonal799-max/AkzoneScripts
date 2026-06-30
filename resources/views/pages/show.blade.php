@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 pb-16 sm:px-6 lg:px-8">
    <div class="card p-6 sm:p-10">
        <h1 class="font-display text-3xl font-extrabold tracking-tight text-ink-900">{{ $page->title }}</h1>
        <p class="mt-1 text-sm text-slate-400">Last updated {{ $page->updated_at->format('M j, Y') }}</p>
        <div class="prose prose-slate mt-6 max-w-none whitespace-pre-line text-slate-600">{!! nl2br(e($page->content)) !!}</div>
    </div>
</div>
@endsection
