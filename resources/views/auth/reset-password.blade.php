<x-guest-layout>
    <h1 class="font-display text-3xl font-extrabold text-ink-900">Reset password</h1>
    <p class="mt-2 text-sm text-slate-500">Choose a new password for your account.</p>

    <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label for="email" class="label">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required class="input">
        </div>
        <div>
            <label for="password" class="label">New password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" class="input" placeholder="••••••••">
        </div>
        <div>
            <label for="password_confirmation" class="label">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="input" placeholder="••••••••">
        </div>
        <button type="submit" class="btn-primary btn-lg w-full">Reset password</button>
    </form>
</x-guest-layout>
