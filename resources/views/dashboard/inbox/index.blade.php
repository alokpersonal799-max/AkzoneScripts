@extends('layouts.dashboard')

@section('title', 'Inbox')

@section('dashboard')
<div class="flex items-center justify-between">
    <div>
        <h1 class="font-display text-2xl font-extrabold text-ink-900">Inbox</h1>
        <p class="mt-1 text-sm text-slate-500">Announcements and updates from {{ setting('site_name', config('app.name')) }}.</p>
    </div>
    @if ($unread > 0)
        <span class="rounded-full bg-brand-100 px-3 py-1 text-sm font-bold text-brand-700">{{ $unread }} unread</span>
    @endif
</div>

<div class="mt-6 space-y-3">
    @forelse ($recipients as $rec)
        @php $a = $rec->announcement; $meta = $a->themeMeta(); @endphp
        <a href="{{ route('inbox.show', $a) }}" class="card flex items-start gap-3 p-4 transition hover:shadow-lift {{ $rec->read_at ? '' : 'ring-2 ring-brand-500/20' }}">
            <span class="flex h-10 w-10 flex-none items-center justify-center rounded-xl bg-{{ $meta['color'] }}-100 text-{{ $meta['color'] }}-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}" /></svg>
            </span>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <p class="truncate font-semibold text-ink-900">{{ $a->title }}</p>
                    @if (! $rec->read_at)<span class="h-2 w-2 flex-none rounded-full bg-brand-500"></span>@endif
                </div>
                <p class="mt-0.5 line-clamp-2 text-sm text-slate-500">{{ \Illuminate\Support\Str::limit(strip_tags($a->body), 120) }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $a->sent_at?->diffForHumans() }}</p>
            </div>
        </a>
    @empty
        <div class="card p-10 text-center">
            <p class="text-slate-500">Your inbox is empty. Announcements will appear here.</p>
        </div>
    @endforelse
</div>

<div class="mt-6">{{ $recipients->links() }}</div>
@endsection
