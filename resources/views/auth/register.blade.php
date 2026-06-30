<x-guest-layout>
    <h1 class="font-display text-3xl font-extrabold text-ink-900">Create your account</h1>
    <p class="mt-2 text-sm text-slate-500">Join {{ config('marketplace.name') }} and start downloading premium assets.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="name" class="label">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="input" placeholder="Jane Developer">
        </div>

        <div>
            <label for="email" class="label">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                   class="input" placeholder="you@example.com">
        </div>

        <div>
            <label for="password" class="label">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                   class="input" placeholder="••••••••">
        </div>

        <div>
            <label for="password_confirmation" class="label">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                   class="input" placeholder="••••••••">
        </div>

        <button type="submit" class="btn-primary btn-lg w-full">Create account</button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Sign in</a>
    </p>
</x-guest-layout>
