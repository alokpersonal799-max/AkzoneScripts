@extends('layouts.admin')

@section('page-title', 'Announcement')

@section('admin')
@php $meta = $announcement->themeMeta(); @endphp
<div class="mx-auto max-w-3xl space-y-6">
    <a href="{{ route('admin.announcements.index') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to announcements</a>

    {{-- Announcement --}}
    <div class="card overflow-hidden">
        <div class="flex items-center gap-3 border-b border-slate-100 bg-{{ $meta['color'] }}-50/60 p-5">
            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-{{ $meta['color'] }}-100 text-{{ $meta['color'] }}-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}" /></svg>
            </span>
            <div>
                <span class="rounded-full bg-{{ $meta['color'] }}-100 px-2 py-0.5 text-[11px] font-bold text-{{ $meta['color'] }}-700">{{ $meta['label'] }}</span>
                <h1 class="mt-1 font-display text-xl font-extrabold text-ink-900">{{ $announcement->title }}</h1>
            </div>
        </div>
        <div class="p-5">
            <p class="whitespace-pre-line text-sm text-slate-600">{{ $announcement->body }}</p>
            @if ($announcement->action_url)
                <a href="{{ $announcement->action_url }}" target="_blank" rel="noopener" class="mt-3 inline-block text-sm font-semibold text-brand-600 hover:underline">{{ $announcement->action_url }}</a>
            @endif
        </div>
        <div class="grid grid-cols-2 divide-x divide-slate-100 border-t border-slate-100 text-center sm:grid-cols-4">
            <div class="p-4"><p class="text-xs text-slate-400">Recipients</p><p class="font-display text-lg font-bold text-ink-900">{{ $announcement->recipients_count }}</p></div>
            <div class="p-4"><p class="text-xs text-slate-400">Read</p><p class="font-display text-lg font-bold text-ink-900">{{ $announcement->read_count }}</p></div>
            <div class="p-4"><p class="text-xs text-slate-400">Audience</p><p class="text-sm font-semibold text-ink-900">{{ $announcement->audience === 'all' ? 'All' : 'Selected' }}</p></div>
            <div class="p-4"><p class="text-xs text-slate-400">Status</p><p class="text-sm font-semibold text-ink-900 capitalize">{{ $announcement->status }}</p></div>
        </div>
    </div>

    {{-- Admin reply --}}
    @if ($announcement->allow_reply)
        <div class="card p-5">
            <h2 class="text-sm font-bold text-ink-900">Post a reply (visible to recipients)</h2>
            <form method="POST" action="{{ route('admin.announcements.reply', $announcement) }}" class="mt-3">
                @csrf
                <textarea name="message" rows="2" required class="input" placeholder="Write a reply to your users..."></textarea>
                <button type="submit" class="btn-primary btn-sm mt-2">Send reply</button>
            </form>
        </div>
    @endif

    {{-- Replies / feedback --}}
    <div class="card p-5">
        <h2 class="text-sm font-bold text-ink-900">Replies & feedback ({{ $replies->total() }})</h2>
        <div class="mt-4 space-y-4">
            @forelse ($replies as $r)
                <div class="flex gap-3">
                    <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full text-xs font-bold text-white {{ $r->is_admin ? 'bg-brand-600' : 'bg-slate-400' }}">
                        {{ strtoupper(substr($r->is_admin ? 'Admin' : ($r->user->name ?? 'U'), 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1 rounded-xl {{ $r->is_admin ? 'bg-brand-50' : 'bg-slate-50' }} px-4 py-2.5">
                        <p class="text-xs font-semibold text-ink-900">{{ $r->is_admin ? 'You (admin)' : ($r->user->name ?? 'User') }} <span class="font-normal text-slate-400">· {{ $r->created_at->diffForHumans() }}</span></p>
                        @if ($r->type === 'star')
                            <p class="mt-1 text-amber-400">{!! str_repeat('★', (int) $r->rating).str_repeat('☆', 5 - (int) $r->rating) !!}</p>
                        @elseif ($r->type === 'emoji')
                            <p class="mt-1 text-2xl">{{ $r->emoji }}</p>
                        @elseif ($r->type === 'media' && $r->media_url)
                            <a href="{{ $r->media_url }}" target="_blank" rel="noopener"><img src="{{ $r->media_url }}" alt="reply media" class="mt-1 max-h-40 rounded-lg"></a>
                        @endif
                        @if ($r->message)<p class="mt-1 text-sm text-slate-600">{{ $r->message }}</p>@endif
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400">No replies yet.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $replies->links() }}</div>
    </div>
</div>
@endsection
