@extends('layouts.admin')

@section('page-title', 'Activity Log')

@section('admin')
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="font-display text-2xl font-extrabold text-ink-900">Activity Log</h1>
        <p class="mt-1 text-sm text-slate-500">Audit trail — who changed what, and when.</p>
    </div>
    <form method="POST" action="{{ route('admin.activity.clear') }}" onsubmit="return confirm('Clear the entire activity log?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn-ghost btn-sm text-rose-600 hover:bg-rose-50">Clear log</button>
    </form>
</div>

{{-- Filters --}}
<form method="GET" class="card mb-6 flex flex-wrap items-end gap-3 p-4">
    <div>
        <label class="label">Action</label>
        <select name="action" class="input w-40">
            <option value="">All actions</option>
            @foreach (['created', 'updated', 'deleted'] as $a)
                <option value="{{ $a }}" {{ $filterAction === $a ? 'selected' : '' }}>{{ ucfirst($a) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">Type</label>
        <select name="type" class="input w-44">
            <option value="">All types</option>
            @foreach ($types as $t)
                <option value="{{ $t }}" {{ $filterType === $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn-primary btn-md">Filter</button>
    <a href="{{ route('admin.activity.index') }}" class="btn-ghost btn-md">Reset</a>
</form>

<div class="card overflow-hidden">
    <div class="divide-y divide-slate-100">
        @forelse ($logs as $log)
            <div class="flex items-center gap-4 p-4">
                <span class="chip flex-shrink-0 ring-1 {{ $log->action_color }}">{{ ucfirst($log->action) }}</span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm text-ink-900">
                        <span class="font-semibold">{{ $log->user_name }}</span>
                        {{ $log->action }}
                        <span class="font-semibold">{{ $log->subject_type }}</span>
                        <span class="text-slate-500">"{{ \Illuminate\Support\Str::limit($log->subject_label, 50) }}"</span>
                    </p>
                    <p class="mt-0.5 text-xs text-slate-400">{{ $log->created_at->format('M j, Y g:i A') }} · {{ $log->created_at->diffForHumans() }} @if ($log->ip) · {{ $log->ip }} @endif</p>
                </div>
            </div>
        @empty
            <p class="p-8 text-center text-sm text-slate-400">No activity recorded yet.</p>
        @endforelse
    </div>
</div>

<div class="mt-6">{{ $logs->links() }}</div>
@endsection
