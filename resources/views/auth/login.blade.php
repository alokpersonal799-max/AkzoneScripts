<x-guest-layout>
    <h1 class="font-display text-3xl font-bold text-white">Welcome back</h1>
    <p class="mt-2 text-sm text-slate-400">Sign in to access your downloads and dashboard.</p>

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-slate-300">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="mt-2 block w-full rounded-xl border border-white/10 bg-ink-800 px-4 py-3 text-white placeholder-slate-500 transition focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20"
                   placeholder="you@example.com">
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
            </div>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="mt-2 block w-full rounded-xl border border-white/10 bg-ink-800 px-4 py-3 text-white placeholder-slate-500 transition focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20"
                   placeholder="••••••••">
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-400">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/20 bg-ink-800 text-brand-500 focus:ring-brand-400/30">
            Remember me
        </label>

        <button type="submit" class="w-full rounded-xl bg-brand-400 px-4 py-3 font-semibold text-ink-900 transition hover:bg-brand-300 focus:ring-2 focus:ring-brand-400/40">
            Sign in
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-400">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-semibold text-brand-300 hover:text-brand-200">Create one</a>
    </p>
</x-guest-layout>
