@extends('layouts.admin')

@section('page-title', 'Customer details')

@section('admin')
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to customers</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[320px_1fr]">
        {{-- Profile --}}
        <div class="space-y-6">
            <div class="card p-6 text-center">
                <span class="mx-auto flex h-20 w-20 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-brand-500 to-indigo-500 text-3xl font-bold text-white">
                    @if ($user->avatar)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) }}" alt="" class="h-full w-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </span>
                <h2 class="mt-4 font-display text-xl font-bold text-ink-900">{{ $user->name }}</h2>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                <div class="mt-3 flex justify-center"><x-status-badge :status="$user->role" /></div>
                @if ($user->bio)
                    <p class="mt-4 text-sm text-slate-500">{{ $user->bio }}</p>
                @endif
                <dl class="mt-6 grid grid-cols-2 gap-4 border-t border-slate-100 pt-6 text-left text-sm">
                    <div><dt class="text-slate-400">Orders</dt><dd class="font-bold text-ink-900">{{ $user->orders_count }}</dd></div>
                    <div><dt class="text-slate-400">Joined</dt><dd class="font-bold text-ink-900">{{ $user->created_at->format('M Y') }}</dd></div>
                </dl>
            </div>

            <div class="card p-6">
                <h3 class="font-display text-base font-bold text-ink-900">Role</h3>
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-4 flex gap-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <select name="role" class="input flex-1">
                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    <button type="submit" class="btn-primary btn-sm">Save</button>
                </form>

                <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-ghost btn-sm">Edit details</a>
                    <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="rounded-xl border px-4 py-2 text-sm font-semibold transition {{ $user->hasVerifiedEmail() ? 'border-slate-200 text-slate-600 hover:bg-slate-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                            {{ $user->hasVerifiedEmail() ? 'Unverify email' : 'Mark verified' }}
                        </button>
                    </form>
                    @if ($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="rounded-xl border px-4 py-2 text-sm font-semibold transition {{ $user->is_banned ? 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' : 'border-amber-200 text-amber-600 hover:bg-amber-50' }}">
                                {{ $user->is_banned ? 'Unban user' : 'Ban user' }}
                            </button>
                        </form>
                    @endif
                </div>

                @if ($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-3" onsubmit="return confirm('Delete this user account? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50">Delete account</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Orders --}}
        <div class="card overflow-hidden">
            <div class="border-b border-slate-100 p-5">
                <h3 class="font-display text-base font-bold text-ink-900">Order history</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($orders as $order)
                    <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between p-5 transition hover:bg-slate-50">
                        <div>
                            <p class="font-mono text-sm font-semibold text-brand-600">{{ $order->order_number }}</p>
                            <p class="mt-1 text-xs text-slate-400">{{ $order->items_count }} items · {{ $order->created_at->format('M j, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-ink-900">{{ base_symbol() }}{{ number_format($order->total, 2) }}</p>
                            <x-status-badge :status="$order->status" class="mt-1" />
                        </div>
                    </a>
                @empty
                    <p class="p-5 text-sm text-slate-500">No orders yet.</p>
                @endforelse
            </div>
            <div class="p-5">{{ $orders->links() }}</div>
        </div>
    </div>
@endsection
