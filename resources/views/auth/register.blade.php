<x-guest-layout>
    <h1 class="font-display text-3xl font-bold text-white">Create your account</h1>
    <p class="mt-2 text-sm text-slate-400">Join {{ config('marketplace.name') }} and start downloading premium assets.</p>

    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-slate-300">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="mt-2 block w-full rounded-xl border border-white/10 bg-ink-800 px-4 py-3 text-white placeholder-slate-500 transition focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20"
                   placeholder="Jane Developer">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-slate-300">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                   class="mt-2 block w-full rounded-xl border border-white/10 bg-ink-800 px-4 py-3 text-white placeholder-slate-500 transition focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20"
                   placeholder="you@example.com">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password"
                   class="mt-2 block w-full rounded-xl border border-white/10 bg-ink-800 px-4 py-3 text-white placeholder-slate-500 transition focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20"
                   placeholder="••••••••">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-300">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                   class="mt-2 block w-full rounded-xl border border-white/10 bg-ink-800 px-4 py-3 text-white placeholder-slate-500 transition focus:border-brand-400 focus:ring-2 focus:ring-brand-400/20"
                   placeholder="••••••••">
        </div>

        <button type="submit" class="w-full rounded-xl bg-brand-400 px-4 py-3 font-semibold text-ink-900 transition hover:bg-brand-300 focus:ring-2 focus:ring-brand-400/40">
            Create account
        </button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-400">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-brand-300 hover:text-brand-200">Sign in</a>
    </p>
</x-guest-layout>
