@extends('layouts.admin')

@section('page-title', 'Reports')

@section('admin')
    <form method="GET" action="{{ route('admin.reports.index') }}" class="mb-6 flex flex-wrap items-center gap-2">
        <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-500 focus:outline-none">
            <option value="">All statuses</option>
            @foreach (['pending', 'reviewing', 'resolved', 'dismissed'] as $st)
                <option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
            @endforeach
        </select>
        <span class="text-sm text-slate-500">{{ $pendingCount }} pending</span>
    </form>

    <div class="space-y-4">
        @forelse ($reports as $report)
            <div class="card p-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-ink-900">{{ $report->reason }}</span>
                            <x-status-badge :status="$report->status === 'resolved' ? 'completed' : ($report->status === 'dismissed' ? 'archived' : 'pending')" />
                        </div>
                        <p class="mt-1 text-sm text-slate-500">
                            Product:
                            @if ($report->product)
                                <a href="{{ route('products.show', $report->product) }}" target="_blank" class="font-medium text-brand-600 hover:underline">{{ $report->product->title }}</a>
                            @else
                                <span class="text-slate-400">deleted</span>
                            @endif
                            · by {{ $report->user?->name ?? 'Guest' }} · {{ $report->created_at->diffForHumans() }}
                        </p>
                        @if ($report->details)
                            <p class="mt-2 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ $report->details }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('admin.reports.destroy', $report) }}" onsubmit="return confirm('Delete this report?');">
                        @csrf @method('DELETE')
                        <button class="text-sm font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                    </form>
                </div>

                <form method="POST" action="{{ route('admin.reports.update', $report) }}" class="mt-4 flex flex-wrap items-end gap-2 border-t border-slate-100 pt-4">
                    @csrf @method('PATCH')
                    <div>
                        <label class="label">Status</label>
                        <select name="status" class="input">
                            @foreach (['pending', 'reviewing', 'resolved', 'dismissed'] as $st)
                                <option value="{{ $st }}" {{ $report->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="label">Admin note</label>
                        <input name="admin_note" value="{{ $report->admin_note }}" class="input" placeholder="Optional note">
                    </div>
                    <button type="submit" class="btn-primary btn-sm">Save</button>
                </form>
            </div>
        @empty
            <x-empty-state title="No reports" message="When customers report a product, it will show here." />
        @endforelse
    </div>

    <div class="mt-6">{{ $reports->links() }}</div>
@endsection
