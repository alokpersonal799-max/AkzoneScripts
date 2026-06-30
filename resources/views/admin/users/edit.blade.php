@extends('layouts.admin')

@section('page-title', 'Edit user')

@section('admin')
    <div class="mb-6">
        <a href="{{ route('admin.users.show', $user) }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to user</a>
        <h2 class="mt-2 font-display text-2xl font-extrabold text-ink-900">Edit {{ $user->name }}</h2>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="card max-w-2xl space-y-5 p-6 sm:p-8">
        @csrf @method('PATCH')
        <div class="grid gap-5 sm:grid-cols-2">
            <div><label for="name" class="label">Name</label><input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="input"></div>
            <div><label for="email" class="label">Email</label><input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="input"></div>
        </div>
        <div>
            <label for="role" class="label">Role</label>
            <select id="role" name="role" class="input">
                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div><label for="password" class="label">New password <span class="text-slate-400">(leave blank to keep)</span></label><input id="password" name="password" type="password" class="input"></div>
            <div><label for="password_confirmation" class="label">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" class="input"></div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary btn-md">Save changes</button>
            <a href="{{ route('admin.users.show', $user) }}" class="btn-ghost btn-md">Cancel</a>
        </div>
    </form>
@endsection
