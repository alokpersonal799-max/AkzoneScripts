@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('admin')
    @php $sym = base_symbol(); @endphp

    {{-- Toolbar --}}
    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="font-display text-2xl font-extrabold text-ink-900">Dashboard</h1>
            <p class="text-sm text-slate-500">Welcome back, {{ auth()->user()->name }}.</p>
        </div>
        <button type="button" x-data="{ spin: false }" @click="spin = true; window.location.reload()"
                class="inline-flex flex-none items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-brand-300 hover:text-brand-600"
                title="Refresh dashboard">
            <svg class="h-4 w-4" :class="spin && 'animate-spin'" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
            <span class="hidden sm:inline">Refresh</span>
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $cards = [
                ['label' => 'Revenue', 'value' => $sym.number_format($stats['revenue'], 2), 'sub' => $stats['orders'].' completed · '.$stats['orders_pending'].' pending', 'tint' => 'bg-emerald-50 text-emerald-600', 'icon' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33'],
                ['label' => "Today's revenue", 'value' => $sym.number_format($stats['revenue_today'], 2), 'sub' => $stats['orders_today'].' orders · '.$stats['customers_today'].' new users today', 'tint' => 'bg-amber-50 text-amber-600', 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z'],
                ['label' => 'Total sold', 'value' => number_format($stats['sold']), 'sub' => number_format($stats['downloads']).' free downloads', 'tint' => 'bg-emerald-50 text-emerald-600', 'icon' => 'm3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z'],
                ['label' => 'Total views', 'value' => number_format($stats['views']), 'sub' => 'all-time product views', 'tint' => 'bg-sky-50 text-sky-600', 'icon' => 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178ZM15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'],
                ['label' => 'Customers', 'value' => number_format($stats['customers']), 'sub' => $stats['published'].' published · '.$stats['free_products'].' free', 'tint' => 'bg-brand-50 text-brand-600', 'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Z'],
                ['label' => 'Active services', 'value' => number_format($stats['services']), 'sub' => 'live on the services page', 'tint' => 'bg-fuchsia-50 text-fuchsia-600', 'icon' => 'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437'],
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

    {{-- Announcements snapshot --}}
    @if ($announcementStats)
        <div class="mt-6 card overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 p-5">
                <div class="flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535" /></svg>
                    </span>
                    <h2 class="font-display text-lg font-bold text-ink-900">Announcements</h2>
                </div>
                <a href="{{ route('admin.announcements.index') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Manage</a>
            </div>
            <div class="grid grid-cols-2 gap-px bg-slate-100 sm:grid-cols-4">
                @foreach ([['Total', $announcementStats['total']], ['Sent', $announcementStats['sent']], ['Scheduled', $announcementStats['scheduled']], ['User replies', $announcementStats['replies']]] as $st)
                    <div class="bg-white p-4">
                        <p class="text-xs text-slate-400">{{ $st[0] }}</p>
                        <p class="font-display text-xl font-bold text-ink-900">{{ number_format($st[1]) }}</p>
                    </div>
                @endforeach
            </div>
            @if ($announcementStats['recent']->isNotEmpty())
                <div class="divide-y divide-slate-100 border-t border-slate-100">
                    @foreach ($announcementStats['recent'] as $a)
                        <a href="{{ route('admin.announcements.show', $a) }}" class="flex items-center justify-between px-5 py-3 transition hover:bg-slate-50">
                            <span class="truncate text-sm font-medium text-ink-900">{{ $a->title }}</span>
                            <span class="ml-3 flex-none text-xs font-semibold capitalize {{ $a->status === 'sent' ? 'text-emerald-600' : ($a->status === 'scheduled' ? 'text-indigo-600' : 'text-slate-400') }}">{{ $a->status }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

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

    {{-- Country analytics --}}
    @if ($topPurchasingCountries->isNotEmpty() || $topBrowsingCountries->isNotEmpty())
        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            {{-- Top purchasing countries --}}
            <div class="card p-5">
                <h2 class="font-display text-lg font-bold text-ink-900">Top Purchasing Countries</h2>
                <p class="text-xs text-slate-400">By completed-order revenue</p>
                @php $maxRev = $topPurchasingCountries->max('revenue') ?: 1; @endphp
                <div class="mt-4 space-y-3">
                    @forelse ($topPurchasingCountries as $c)
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 text-ink-900">
                                    <span class="text-lg leading-none">{{ country_flag($c->billing_country) }}</span>
                                    <span class="font-medium">{{ country_name($c->billing_country) }}</span>
                                    <span class="text-xs text-slate-400">({{ $c->orders }})</span>
                                </span>
                                <span class="font-bold text-emerald-600">{{ money($c->revenue) }}</span>
                            </div>
                            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-emerald-500" style="width: {{ max(4, round(($c->revenue / $maxRev) * 100)) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-slate-400">No completed orders with a country yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Top browsing countries --}}
            <div class="card p-5">
                <h2 class="font-display text-lg font-bold text-ink-900">Top Browsing Countries</h2>
                <p class="text-xs text-slate-400">By storefront page views</p>
                @php $maxViews = $topBrowsingCountries->max('views') ?: 1; @endphp
                <div class="mt-4 space-y-3">
                    @forelse ($topBrowsingCountries as $c)
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 text-ink-900">
                                    <span class="text-lg leading-none">{{ country_flag($c->code) }}</span>
                                    <span class="font-medium">{{ country_name($c->code) }}</span>
                                </span>
                                <span class="font-bold text-brand-600">{{ number_format($c->views) }} views</span>
                            </div>
                            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-brand-500" style="width: {{ max(4, round(($c->views / $maxViews) * 100)) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-center text-sm text-slate-400">No browsing data yet. Country is detected via your CDN/proxy (e.g. Cloudflare) in production.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

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

    {{-- Demonstration data tool (kept at the bottom, below System Health) --}}
    @php
        $demoImports = (int) setting('demo_import_uses', 0);
        $demoMaxImports = \App\Http\Controllers\Admin\DemoDataController::MAX_IMPORTS;
        $demoImportsLeft = max(0, $demoMaxImports - $demoImports);
        $demoHidden = setting('demo_tool_hidden', '0') === '1';
    @endphp
    @if (! $demoHidden)
        <div x-data="{ show: @js($errors->hasAny(['admin_email', 'admin_password']) ? 'clear' : null) }" class="mt-6 rounded-2xl border border-dashed border-amber-300 bg-amber-50/70 p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 flex-none items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" /></svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-ink-900">Demonstration data tool</p>
                        <p class="mt-0.5 text-xs text-amber-700"><strong>For demonstration only.</strong> Import sample content to explore features (max {{ $demoMaxImports }} times — <strong>{{ $demoImportsLeft }} left</strong>), and clear it any time. Hide this tool permanently when you go live.</p>
                    </div>
                </div>
                <div class="flex flex-none flex-wrap gap-2">
                    @if ($demoImportsLeft > 0)
                        <button type="button" @click="show = 'import'" class="btn-primary btn-sm">Import demo data</button>
                    @else
                        <span class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-400">Import limit reached</span>
                    @endif
                    <button type="button" @click="show = 'clear'" class="btn-ghost btn-sm border border-rose-200 text-rose-600 hover:bg-rose-50">Clear demo data</button>
                    <button type="button" @click="show = 'hide'" class="btn-ghost btn-sm border border-slate-200 text-slate-500 hover:bg-slate-100">Hide permanently</button>
                </div>
            </div>

            {{-- Confirmation modal --}}
            <div x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
                <div class="absolute inset-0 bg-ink-900/50" @click="show = null"></div>
                <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" @keydown.escape.window="show = null">
                    {{-- Import confirm --}}
                    <div x-show="show === 'import'">
                        <h3 class="font-display text-lg font-bold text-ink-900">Import demo data?</h3>
                        <div class="mt-3 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-700">
                            <p class="font-semibold">Please note</p>
                            <p class="mt-1">This adds sample categories, products, gallery images, reviews and demo accounts (including <code class="font-mono">admin@akzone.com</code> / <code class="font-mono">password</code>).</p>
                            <p class="mt-1"><strong>For demonstration only.</strong> Demo data can be imported a maximum of {{ $demoMaxImports }} times — you have <strong>{{ $demoImportsLeft }} left</strong>.</p>
                        </div>
                        <div class="mt-5 flex justify-end gap-2">
                            <button type="button" @click="show = null" class="btn-ghost btn-md">Cancel</button>
                            <form method="POST" action="{{ route('admin.demo.import') }}">@csrf<button type="submit" class="btn-primary btn-md">Yes, import demo data</button></form>
                        </div>
                    </div>
                    {{-- Clear confirm --}}
                    <div x-show="show === 'clear'">
                        <h3 class="font-display text-lg font-bold text-ink-900">Clear demo data &amp; set up your admin</h3>
                        <div class="mt-3 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            <p class="font-semibold">This cannot be undone</p>
                            <p class="mt-1">Removes all sample products, categories, reviews, services, pages, adverts and demo accounts.</p>
                        </div>
                        <div class="mt-3 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            <p>You're switching to <strong>real business use</strong>. The demo login is public, so set your <strong>own admin email &amp; password</strong> now. You can change them later in Settings — but <strong>don't forget them</strong>: if lost, you'd have to reinstall the script.</p>
                        </div>
                        <form method="POST" action="{{ route('admin.demo.clear') }}" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Your admin email</label>
                                <input type="email" name="admin_email" required value="{{ old('admin_email', auth()->user()->email) }}"
                                       class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
                                @error('admin_email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="text-xs font-semibold text-slate-600">New password</label>
                                    <input type="password" name="admin_password" required minlength="8" autocomplete="new-password"
                                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
                                    @error('admin_password')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-slate-600">Confirm password</label>
                                    <input type="password" name="admin_password_confirmation" required minlength="8" autocomplete="new-password"
                                           class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
                                </div>
                            </div>
                            <div class="flex justify-end gap-2 pt-1">
                                <button type="button" @click="show = null" class="btn-ghost btn-md">Cancel</button>
                                <button type="submit" class="btn-md rounded-xl bg-rose-600 font-semibold text-white hover:bg-rose-700">Clear &amp; save admin</button>
                            </div>
                        </form>
                    </div>
                    {{-- Hide permanently confirm --}}
                    <div x-show="show === 'hide'">
                        <h3 class="font-display text-lg font-bold text-ink-900">Hide this tool permanently?</h3>
                        <div class="mt-3 rounded-xl bg-slate-100 px-4 py-3 text-sm text-slate-600">
                            <p class="font-semibold text-ink-900">Warning</p>
                            <p class="mt-1">This removes the demo data tool for good. You will <strong>never be able to import or clear demo data again</strong> from the dashboard. If you need it back later, you'd have to <strong>reinstall the script</strong>.</p>
                        </div>
                        <div class="mt-5 flex justify-end gap-2">
                            <button type="button" @click="show = null" class="btn-ghost btn-md">Cancel</button>
                            <form method="POST" action="{{ route('admin.demo.hide') }}">@csrf<button type="submit" class="btn-md rounded-xl bg-slate-800 font-semibold text-white hover:bg-slate-900">Yes, hide it forever</button></form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
