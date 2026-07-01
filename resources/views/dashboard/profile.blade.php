@extends('layouts.dashboard')

@section('dashboard')
    <div class="mb-8">
        <h1 class="font-display text-2xl font-extrabold text-ink-900">Settings</h1>
        <p class="mt-1 text-slate-500">Manage your profile and security.</p>
    </div>

    <div class="space-y-8">
        {{-- Profile information --}}
        <div class="card p-6 sm:p-8">
            <h2 class="font-display text-lg font-bold text-ink-900">Profile information</h2>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-5">
                @csrf
                @method('PATCH')

                <div class="flex items-center gap-4">
                    <span class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-2xl">
                        <img src="{{ $user->avatar_url }}" alt="avatar" class="h-full w-full object-cover">
                    </span>
                    <div>
                        <label for="avatar" class="label">Profile photo</label>
                        <input id="avatar" name="avatar" type="file" accept="image/*"
                               class="block text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-600 hover:file:bg-brand-100">
                        <p class="mt-1 text-xs text-slate-400">Leave empty to use your auto-generated avatar.</p>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="label">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="input">
                    </div>
                    <div>
                        <label for="email" class="label">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="input">
                    </div>
                </div>

                <div>
                    <label class="label">Mobile number</label>
                    <div class="flex gap-2">
                        <select name="phone_country" class="input w-40 flex-shrink-0">
                            @include('partials.country-codes', ['selected' => old('phone_country', $user->phone_country)])
                        </select>
                        <input name="phone" type="tel" value="{{ old('phone', $user->phone) }}" class="input" placeholder="Phone number">
                    </div>
                </div>

                <div>
                    <label for="bio" class="label">Bio</label>
                    <textarea id="bio" name="bio" rows="3" placeholder="Tell us a little about yourself..." class="input">{{ old('bio', $user->bio) }}</textarea>
                </div>

                <button type="submit" class="btn-primary btn-md">Save changes</button>
            </form>
        </div>

        {{-- Password --}}
        <div class="card p-6 sm:p-8">
            <h2 class="font-display text-lg font-bold text-ink-900">Change password</h2>
            <form method="POST" action="{{ route('profile.password') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="label">Current password</label>
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password" class="input max-w-md">
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="label">New password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" class="input">
                    </div>
                    <div>
                        <label for="password_confirmation" class="label">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="input">
                    </div>
                </div>

                <button type="submit" class="btn-primary btn-md">Update password</button>
            </form>
        </div>
    </div>
@endsection
