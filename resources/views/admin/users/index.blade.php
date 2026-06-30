@extends('layouts.admin')

@section('page-title', 'Customers')

@section('admin')
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6 flex flex-wrap items-center gap-2">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search name or email..."
               class="w-full max-w-xs rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">
        <select name="role" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-brand-500 focus:outline-none">
            <option value="">All roles</option>
            <option value="user" {{ ($filters['role'] ?? '') === 'user' ? 'selected' : '' }}>Users</option>
            <option value="admin" {{ ($filters['role'] ?? '') === 'admin' ? 'selected' : '' }}>Admins</option>
        </select>
        <button type="submit" class="btn-ghost btn-sm">Filter</button>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-400">
                    <tr>
                        <th class="px-5 py-3 font-semibold">User</th>
                        <th class="px-5 py-3 font-semibold">Orders</th>
                        <th class="px-5 py-3 font-semibold">Role</th>
                        <th class="px-5 py-3 font-semibold">Joined</th>
                        <th class="px-5 py-3 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-indigo-500 text-sm font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    <div>
                                        <p class="font-semibold text-ink-900">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $user->orders_count }}</td>
                            <td class="px-5 py-3"><x-status-badge :status="$user->role" /></td>
                            <td class="px-5 py-3 text-slate-500">{{ $user->created_at->format('M j, Y') }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Manage</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
