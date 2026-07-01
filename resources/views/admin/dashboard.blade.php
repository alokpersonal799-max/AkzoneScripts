@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('admin')
    @php $sym = base_symbol(); @endphp

    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['label' => 'Revenue', 'value' => $sym.number_format($stats['revenue'], 2), 'sub' => $stats['orders'].' completed orders', 'tint' => 'bg-emerald-50 text-emerald-600', 'icon' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33'],
                ['label' => 'Total sold', 'value' => number_format($stats['sold']), 'sub' => number_format($stats['downloads']).' free downloads', 'tint' => 'bg-emerald-50 text-emerald-600', 'icon' => 'm3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z'],
                ['label' => 'Total views', 'value' => number_format($stats['views']), 'sub' => 'all-time product views', 'tint' => 'bg-sky-50 text-sky-600', 'icon' => 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178ZM15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
                ['label' => 'Customers', 'value' => number_format($stats['customers']), 'sub' => $stats['published'].' published · '.$stats['free_products'].' free', 'tint' => 'bg-brand-50 text-brand-600', 'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Z'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-slate-500">{{ $card['label'] }}</p>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $card['tint'] }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" /></svg>
                    </span>
                </div>
                <p class="mt-3 font-display text-2xl font-extrabold text-ink-900">{{ $card['value'] }}</p>
                <p class="mt-1 text-xs text-slate-400">{{ $card['sub'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.6fr_1fr]">
        {{-- Sales chart --}}
        <div class="card p-6">
            <h2 class="font-display text-lg font-bold text-ink-900">Revenue · last 14 days</h2>
            @php $maxTotal = max($salesByDay->max('total'), 1); @endphp
            <div class="mt-6 flex items-end justify-between gap-1.5" style="height: 200px;">
                @foreach ($salesByDay as $day)
                    <div class="group flex flex-1 flex-col items-center justify-end gap-2">
                        <div class="w-full rounded-t-md bg-gradient-to-t from-brand-500 to-brand-300 transition-all hover:from-brand-600 hover:to-brand-400"
                             style="height: {{ max(($day['total'] / $maxTotal) * 160, 4) }}px;" title="{{ $sym }}{{ number_format($day['total'], 2) }}"></div>
                        <span class="text-[10px] text-slate-400">{{ \Illuminate\Support\Str::of($day['label'])->explode(' ')->last() }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Top categories --}}
        <div class="card overflow-hidden">
            <div class="border-b border-slate-100 p-5">
                <h2 class="font-display text-lg font-bold text-ink-900">Top categories</h2>
                <p class="text-xs text-slate-400">By revenue</p>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($topCategories as $cat)
                    <div class="flex items-center justify-between p-4">
                        <div>
                            <p class="text-sm font-semibold text-ink-900">{{ $cat->name }}</p>
                            <p class="text-xs text-slate-400">{{ (int) $cat->sales }} sales</p>
                        </div>
                        <p class="font-bold text-ink-900">{{ $sym }}{{ number_format((float) $cat->revenue, 2) }}</p>
                    </div>
                @empty
                    <p class="p-5 text-sm text-slate-500">No sales data yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        {{-- Recent orders --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 p-5">
                <h2 class="font-display text-lg font-bold text-ink-900">Recent orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">View all</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between p-5 transition hover:bg-slate-50">
                        <div class="min-w-0">
                            <p class="font-mono text-sm font-semibold text-brand-600">{{ $order->order_number }}</p>
                            <p class="mt-1 truncate text-xs text-slate-400">{{ $order->user?->name ?? 'Guest' }} · {{ $order->created_at->format('M j') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-ink-900">{{ $sym }}{{ number_format($order->total, 2) }}</p>
                            <x-status-badge :status="$order->status" class="mt-1" />
                        </div>
                    </a>
                @empty
                    <p class="p-5 text-sm text-slate-500">No orders yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Top products --}}
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 p-5">
                <h2 class="font-display text-lg font-bold text-ink-900">Top products</h2>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Manage</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($topProducts as $product)
                    <div class="flex items-center gap-3 p-5">
                        <img src="{{ $product->thumbnail_url }}" alt="" class="h-12 w-16 flex-shrink-0 rounded-lg object-cover">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-ink-900">{{ $product->title }}</p>
                            <p class="text-xs text-slate-400">{{ number_format($product->sales) }} sold · {{ number_format($product->views) }} views</p>
                        </div>
                        <x-status-badge :status="$product->status" />
                    </div>
                @empty
                    <p class="p-5 text-sm text-slate-500">No products yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent registrations + Recently active --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 p-5">
                <h2 class="font-display text-lg font-bold text-ink-900">Recent registrations</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">View all</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentUsers as $user)
                    <a href="{{ route('admin.users.show', $user) }}" class="flex items-center justify-between p-4 transition hover:bg-slate-50">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-indigo-500 text-sm font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            <div>
                                <p class="text-sm font-semibold text-ink-900">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-400">{{ $user->created_at->diffForHumans() }}</p>
                            @if (! $user->hasVerifiedEmail())<span class="chip mt-1 bg-amber-50 text-amber-700 ring-1 ring-amber-200">Unverified</span>@endif
                        </div>
                    </a>
                @empty
                    <p class="p-5 text-sm text-slate-500">No customers yet.</p>
                @endforelse
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 p-5">
                <h2 class="font-display text-lg font-bold text-ink-900">Recently active</h2>
                <span class="text-xs text-slate-400">Latest sign-ins</span>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentLogins as $user)
                    <a href="{{ route('admin.users.show', $user) }}" class="flex items-center justify-between p-4 transition hover:bg-slate-50">
                        <div class="flex items-center gap-3">
                            <span class="relative flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-sm font-bold text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-ink-900">{{ $user->name }}
                                    @if ($user->isAdmin())<span class="chip ml-1 bg-brand-50 text-brand-700 ring-1 ring-brand-200">Admin</span>@endif
                                </p>
                                <p class="text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-semibold text-emerald-600">{{ $user->last_login_at?->diffForHumans() }}</p>
                            @if ($user->last_login_ip)<p class="text-[11px] text-slate-400">{{ $user->last_login_ip }}</p>@endif
                        </div>
                    </a>
                @empty
                    <p class="p-5 text-sm text-slate-500">No sign-ins recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    @if ($lowStockOrDraft->isNotEmpty())
        <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <h2 class="font-display text-base font-bold text-amber-700">Draft products awaiting publish</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($lowStockOrDraft as $product)
                    <a href="{{ route('admin.products.edit', $product) }}" class="rounded-lg border border-amber-200 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-amber-100">{{ $product->title }}</a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- System Health Quick Check --}}
    <div class="mt-6 card overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 p-5">
            <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                </span>
                <h2 class="font-display text-lg font-bold text-ink-900">System Health</h2>
            </div>
            <a href="{{ route('admin.system.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Full details</a>
        </div>
        <div class="grid grid-cols-2 gap-px bg-slate-100 sm:grid-cols-3 lg:grid-cols-4">
            @php
                $healthChecks = [
                    ['label' => 'PHP', 'ok' => version_compare(PHP_VERSION, '8.2.0', '>='), 'detail' => PHP_VERSION],
                    ['label' => 'Database', 'ok' => true, 'detail' => 'Connected'],
                    ['label' => 'Storage', 'ok' => is_writable(storage_path('app')), 'detail' => is_writable(storage_path('app')) ? 'Writable' : 'Error'],
                    ['label' => 'Debug', 'ok' => !config('app.debug'), 'detail' => config('app.debug') ? 'ON' : 'OFF'],
                ];
                try { \Illuminate\Support\Facades\DB::connection()->getPdo(); } catch (\Throwable $e) { $healthChecks[1]['ok'] = false; $healthChecks[1]['detail'] = 'Failed'; }
            @endphp
            @foreach ($healthChecks as $hc)
                <div class="flex items-center gap-3 bg-white p-4">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full {{ $hc['ok'] ? 'bg-emerald-50' : 'bg-rose-50' }}">
                        @if ($hc['ok'])
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        @else
                            <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                        @endif
                    </span>
                    <div>
                        <p class="text-xs text-slate-500">{{ $hc['label'] }}</p>
                        <p class="text-sm font-semibold {{ $hc['ok'] ? 'text-ink-900' : 'text-rose-600' }}">{{ $hc['detail'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
