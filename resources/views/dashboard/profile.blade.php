@extends('layouts.dashboard')

@section('dashboard')
    <div class="mb-8">
        <h1 class="font-display text-2xl font-bold text-white">Settings</h1>
        <p class="mt-1 text-slate-400">Manage your profile and security.</p>
    </div>

    <div class="space-y-8">
        {{-- Profile information --}}
        <div class="rounded-2xl border border-white/5 bg-ink-800 p-6 sm:p-8">
            <h2 class="font-display text-lg font-bold text-white">Profile information</h2>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf
                @method('PATCH')

                <div class="flex items-center gap-4">
                    <span class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-brand-400 to-indigo-500 text-2xl font-bold text-ink-900">
                        @if ($user->avatar)
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) }}" alt="avatar" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </span>
                    <div>
                        <label for="avatar" class="block text-sm font-medium text-slate-300">Profile photo</label>
                        <input id="avatar" name="avatar" type="file" accept="image/*"
                               class="mt-1 block text-sm text-slate-400 file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-white/20">
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-300">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                               class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-300">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                               class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                    </div>
                </div>

                <div>
                    <label for="bio" class="block text-sm font-medium text-slate-300">Bio</label>
                    <textarea id="bio" name="bio" rows="3" placeholder="Tell us a little about yourself..."
                              class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white placeholder-slate-500 focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">{{ old('bio', $user->bio) }}</textarea>
                </div>

                <button type="submit" class="rounded-xl bg-brand-400 px-5 py-2.5 font-semibold text-ink-900 transition hover:bg-brand-300">Save changes</button>
            </form>
        </div>

        {{-- Password --}}
        <div class="rounded-2xl border border-white/5 bg-ink-800 p-6 sm:p-8">
            <h2 class="font-display text-lg font-bold text-white">Change password</h2>
            <form method="POST" action="{{ route('profile.password') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-300">Current password</label>
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                           class="mt-2 w-full max-w-md rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300">New password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password"
                               class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-300">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                               class="mt-2 w-full rounded-lg border border-white/10 bg-ink-900 px-3 py-2.5 text-white focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20">
                    </div>
                </div>

                <button type="submit" class="rounded-xl bg-brand-400 px-5 py-2.5 font-semibold text-ink-900 transition hover:bg-brand-300">Update password</button>
            </form>
        </div>
    </div>
@endsection
