@extends('layouts.admin')

@section('page-title', 'Customer details')

@section('admin')
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-400 hover:text-brand-300">&larr; Back to customers</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[320px_1fr]">
        {{-- Profile --}}
        <div class="space-y-6">
            <div class="rounded-2xl border border-white/5 bg-ink-800 p-6 text-center">
                <span class="mx-auto flex h-20 w-20 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-brand-400 to-indigo-500 text-3xl font-bold text-ink-900">
                    @if ($user->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) }}" alt="" class="h-full w-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </span>
                <h2 class="mt-4 font-display text-xl font-bold text-white">{{ $user->name }}</h2>
                <p class="text-sm text-slate-400">{{ $user->email }}</p>
                <div class="mt-3 flex justify-center"><x-status-badge :status="$user->role" /></div>
                @if ($user->bio)
                    <p class="mt-4 text-sm text-slate-400">{{ $user->bio }}</p>
                @endif
                <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-white/5 pt-6 text-left text-sm">
                    <div><dt class="text-slate-500">Orders</dt><dd class="font-semibold text-white">{{ $user->orders_count }}</dd></div>
                    <div><dt class="text-slate-500">Joined</dt><dd class="font-semibold text-white">{{ $user->created_at->format('M Y') }}</dd></div>
                </dl>
            </div>

            {{-- Role management --}}
            <div class="rounded-2xl border border-white/5 bg-ink-800 p-6">
                <h3 class="font-display text-base font-bold text-white">Role</h3>
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-4 flex gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="role" class="flex-1 rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-sm text-white focus:border-brand-400">
                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    <button type="submit" class="rounded-lg bg-brand-400 px-4 py-2.5 text-sm font-semibold text-ink-900 hover:bg-brand-300">Save</button>
                </form>

                @if ($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-3" onsubmit="return confirm('Delete this user account? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-lg border border-rose-500/30 px-4 py-2.5 text-sm font-medium text-rose-300 hover:bg-rose-500/10">Delete account</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Orders --}}
        <div class="rounded-2xl border border-white/5 bg-ink-800">
            <div class="border-b border-white/5 p-5">
                <h3 class="font-display text-base font-bold text-white">Order history</h3>
            </div>
            <div class="divide-y divide-white/5">
                @forelse ($orders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between p-5 transition hover:bg-white/5">
                        <div>
                            <p class="font-mono text-sm text-brand-300">{{ $order->order_number }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $order->items_count }} items · {{ $order->created_at->format('M j, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-white">{{ config('marketplace.currency_symbol') }}{{ number_format($order->total, 2) }}</p>
                            <x-status-badge :status="$order->status" />
                        </div>
                    </a>
                @empty
                    <p class="p-5 text-sm text-slate-400">No orders yet.</p>
                @endforelse
            </div>
            <div class="p-5">{{ $orders->links() }}</div>
        </div>
    </div>
@endsection
