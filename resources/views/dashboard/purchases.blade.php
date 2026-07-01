@extends('layouts.dashboard')

@section('dashboard')
    <div class="mb-8">
        <h1 class="font-display text-2xl font-extrabold text-ink-900">My Purchases</h1>
        <p class="mt-1 text-slate-500">Download your products anytime. You have lifetime access.</p>
    </div>

    @if ($orderItems->isEmpty())
        <x-empty-state title="No purchases yet" message="Browse the marketplace to find scripts, code and design assets.">
            <x-slot:action>
                <a href="{{ route('products.index') }}" class="btn-primary btn-md">Explore products</a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="space-y-4">
            @foreach ($orderItems as $item)
                @php
                    $p = $item->product;
                    $canDownload = $item->order->isCompleted() && $p && $p->has_downloadable;
                    $unlimited = $p ? $p->is_unlimited_download : true;
                    $limit = (int) ($p->download_limit ?? 0);
                    $limitReached = $limit > 0 && $item->download_count >= $limit;
                    if ($canDownload && (int) ($p->link_expiry_minutes ?? 0) > 0) {
                        $downloadUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('download', now()->addMinutes((int) $p->link_expiry_minutes), ['orderItem' => $item->id]);
                    } else {
                        $downloadUrl = route('download', $item);
                    }
                @endphp
                <div class="flex flex-col gap-4 rounded-2xl border bg-white p-4 shadow-soft sm:flex-row sm:items-center {{ $canDownload && $unlimited ? 'border-emerald-300 ring-1 ring-emerald-200' : 'border-slate-200' }}">
                    <img src="{{ $p?->thumbnail_url ?? 'https://placehold.co/600x400/eef2ff/2563eb?text=Akzone' }}"
                         alt="{{ $item->product_title }}" class="h-24 w-full flex-shrink-0 rounded-xl object-cover sm:w-36">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-brand-600">{{ $p?->category?->name ?? 'Product' }}</p>
                        <h3 class="font-bold text-ink-900">{{ $item->product_title }}</h3>
                        <p class="mt-1 text-xs text-slate-400">
                            Order <span class="font-mono">{{ $item->order->order_number }}</span> ·
                            {{ $item->created_at->format('M j, Y') }}
                        </p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            @if ($canDownload && $unlimited)
                                <span class="chip bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">♾ Unlimited downloads</span>
                            @elseif ($canDownload)
                                <span class="chip {{ $limitReached ? 'bg-rose-50 text-rose-600 ring-1 ring-rose-200' : 'bg-slate-100 text-slate-600' }}">{{ $item->download_count }} / {{ $limit }} downloads used</span>
                            @endif
                            @if ($p?->is_external_file)
                                <span class="chip bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200">External link</span>
                            @endif
                            @if ($canDownload && (int) ($p->link_expiry_minutes ?? 0) > 0)
                                <span class="chip bg-amber-50 text-amber-700 ring-1 ring-amber-200">Link valid {{ $p->link_expiry_minutes }} min</span>
                            @endif
                        </div>
                        @if ($p?->download_message)
                            <p class="mt-2 rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-500">{{ $p->download_message }}</p>
                        @endif
                    </div>
                    <div class="flex flex-shrink-0 items-center gap-2">
                        @if ($item->order->isCompleted())
                            <a href="{{ route('orders.invoice', $item->order) }}" class="btn-ghost btn-sm inline-flex items-center gap-1" title="Download Invoice">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                Invoice
                            </a>
                        @endif
                        @if ($p)
                            <a href="{{ route('products.show', $p) }}" class="btn-ghost btn-sm">View</a>
                        @endif
                        @if ($canDownload && ! $limitReached)
                            <a href="{{ $downloadUrl }}" class="btn-primary btn-sm" {{ $p->is_external_file ? 'target=_blank rel=noopener' : '' }}>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                {{ $p->is_external_file ? 'Get link' : 'Download' }}
                            </a>
                        @elseif ($limitReached)
                            <span class="rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-500">Limit reached</span>
                        @else
                            <span class="rounded-xl bg-slate-100 px-4 py-2 text-sm text-slate-400">Unavailable</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">{{ $orderItems->links() }}</div>
    @endif
@endsection
