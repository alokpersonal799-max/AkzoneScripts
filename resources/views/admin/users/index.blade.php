@extends('layouts.admin')

@section('page-title', 'Customers')

@section('admin')
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 flex flex-wrap items-center gap-2">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search name or email..."
               class="w-full max-w-xs rounded-lg border border-white/10 bg-ink-800 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
        <select name="role" onchange="this.form.submit()" class="rounded-lg border border-white/10 bg-ink-800 px-3 py-2 text-sm text-white focus:border-brand-400">
            <option value="">All roles</option>
            <option value="user" {{ ($filters['role'] ?? '') === 'user' ? 'selected' : '' }}>Users</option>
            <option value="admin" {{ ($filters['role'] ?? '') === 'admin' ? 'selected' : '' }}>Admins</option>
        </select>
        <button type="submit" class="rounded-lg bg-white/5 px-4 py-2 text-sm font-medium text-white hover:bg-white/10">Filter</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-white/5 bg-ink-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/5 text-sm">
                <thead class="bg-white/5 text-left text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3 font-medium">User</th>
                        <th class="px-5 py-3 font-medium">Orders</th>
                        <th class="px-5 py-3 font-medium">Role</th>
                        <th class="px-5 py-3 font-medium">Joined</th>
                        <th class="px-5 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($users as $user)
                        <tr class="transition hover:bg-white/5">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-brand-400 to-indigo-500 text-sm font-bold text-ink-900">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    <div>
                                        <p class="font-medium text-white">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-slate-300">{{ $user->orders_count }}</td>
                            <td class="px-5 py-3"><x-status-badge :status="$user->role" /></td>
                            <td class="px-5 py-3 text-slate-400">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-sm font-medium text-brand-300 hover:text-brand-200">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
