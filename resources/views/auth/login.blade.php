<x-guest-layout>
    <h1 class="font-display text-3xl font-extrabold text-ink-900">Welcome back</h1>
    <p class="mt-2 text-sm text-slate-500">Sign in to access your downloads and dashboard.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="label">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="input" placeholder="you@example.com">
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="label">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Forgot password?</a>
            </div>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="input" placeholder="••••••••">
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-500">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
            Remember me
        </label>

        @include('partials.captcha')

        <button type="submit" class="btn-primary btn-lg w-full">Sign in</button>
    </form>

    @if (setting('require_email_verification') === '1')
        <form method="POST" action="{{ route('verification.resend') }}" class="mt-4 flex items-center gap-2">
            @csrf
            <input name="email" type="email" placeholder="Resend verification to email" class="input text-sm">
            <button type="submit" class="btn-ghost btn-sm flex-shrink-0">Resend</button>
        </form>
    @endif

    <p class="mt-8 text-center text-sm text-slate-500">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:text-brand-700">Create one</a>
    </p>
</x-guest-layout>
