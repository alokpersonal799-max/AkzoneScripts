@extends('layouts.admin')

@section('page-title', 'Announcements')

@section('admin')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-display text-2xl font-extrabold text-ink-900">Announcements</h1>
            <p class="mt-1 text-sm text-slate-500">Broadcast news, offers and updates to your users' inbox.</p>
        </div>
        <a href="{{ route('admin.announcements.create') }}" class="btn-primary btn-md">New announcement</a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach ([
            ['label' => 'Total', 'value' => $stats['total'], 'color' => 'slate'],
            ['label' => 'Sent', 'value' => $stats['sent'], 'color' => 'emerald'],
            ['label' => 'Scheduled', 'value' => $stats['scheduled'], 'color' => 'indigo'],
            ['label' => 'User replies', 'value' => $stats['replies'], 'color' => 'brand'],
        ] as $s)
            <div class="card p-5">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $s['label'] }}</p>
                <p class="mt-1 font-display text-2xl font-extrabold text-ink-900">{{ number_format($s['value']) }}</p>
            </div>
        @endforeach
    </div>

    {{-- List --}}
    <div class="space-y-3">
        @forelse ($announcements as $a)
            @php $meta = $a->themeMeta(); @endphp
            <div class="card flex flex-col gap-3 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 flex-none items-center justify-center rounded-xl bg-{{ $meta['color'] }}-100 text-{{ $meta['color'] }}-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}" /></svg>
                    </span>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-ink-900">{{ $a->title }}</p>
                            <span class="rounded-full bg-{{ $meta['color'] }}-50 px-2 py-0.5 text-[11px] font-bold text-{{ $meta['color'] }}-700">{{ $meta['label'] }}</span>
                            @if ($a->status === 'scheduled')
                                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-bold text-indigo-700">Scheduled · {{ $a->scheduled_at?->format('M j, g:i A') }}</span>
                            @elseif ($a->status === 'sent')
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700">Sent</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-500">Draft</span>
                            @endif
                        </div>
                        <p class="mt-1 line-clamp-1 text-sm text-slate-500">{{ \Illuminate\Support\Str::limit(strip_tags($a->body), 90) }}</p>
                        <p class="mt-1 text-xs text-slate-400">
                            {{ $a->audience === 'all' ? 'All users' : 'Selected users' }} ·
                            {{ $a->recipients_count }} recipient(s) · {{ $a->read_count }} read · {{ $a->replies_count }} repl{{ $a->replies_count === 1 ? 'y' : 'ies' }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-none items-center gap-2">
                    <a href="{{ route('admin.announcements.show', $a) }}" class="btn-ghost btn-sm">View</a>
                    <form method="POST" action="{{ route('admin.announcements.destroy', $a) }}" onsubmit="return confirm('Delete this announcement?');">
                        @csrf @method('DELETE')
                        <button class="text-sm font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="card p-10 text-center">
                <p class="text-slate-500">No announcements yet.</p>
                <a href="{{ route('admin.announcements.create') }}" class="btn-primary btn-md mt-4">Create your first announcement</a>
            </div>
        @endforelse
    </div>

    {{ $announcements->links() }}
</div>
@endsection
