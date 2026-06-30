@extends('layouts.admin')

@section('page-title', 'System Settings')

@section('admin')
<div class="grid gap-6 lg:grid-cols-[240px_1fr]">
    {{-- Section sub-nav --}}
    <aside class="lg:sticky lg:top-24 lg:self-start">
        <div class="card p-3">
            <p class="px-3 py-2 text-xs font-bold uppercase tracking-wide text-slate-400">System</p>
            <nav class="space-y-1">
                @foreach ($sections as $key => $meta)
                    <a href="{{ route('admin.settings.show', $key) }}"
                       class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $section === $key ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-ink-900' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}" /></svg>
                        {{ $meta['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </aside>

    {{-- Active section --}}
    <div>
        @include('admin.settings.sections.'.$section)
    </div>
</div>
@endsection
