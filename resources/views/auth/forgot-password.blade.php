<x-guest-layout>
    <h1 class="font-display text-3xl font-extrabold text-ink-900">Forgot password?</h1>
    <p class="mt-2 text-sm text-slate-500">Enter your email and we'll send you a link to reset your password.</p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf
        <div>
            <label for="email" class="label">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="input" placeholder="you@example.com">
        </div>
        <button type="submit" class="btn-primary btn-lg w-full">Send reset link</button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        Remembered it?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Back to sign in</a>
    </p>
</x-guest-layout>
