<x-guest-layout>
    <x-slot:subtitle>Sign in to your account</x-slot:subtitle>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="label">Email</label>
            <div class="relative">
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="input pr-10 @error('email') ring-2 ring-rose-300 @enderror" placeholder="you@example.com">
                <svg class="pointer-events-none absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
            </div>
            @error('email')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
        </div>

        <div x-data="{ show: false }">
            <div class="flex items-center justify-between">
                <label for="password" class="label">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">Forgot password?</a>
            </div>
            <div class="relative">
                <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password"
                       class="input pr-10 @error('password') ring-2 ring-rose-300 @enderror" placeholder="••••••••">
                <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" title="Show/hide password">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                    <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                </button>
            </div>
            @error('password')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-500">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
            Remember me
        </label>

        @include('partials.captcha')

        <button type="submit" class="btn-primary btn-lg w-full">Login</button>
    </form>

    @if (setting('require_email_verification') === '1')
        <form method="POST" action="{{ route('verification.resend') }}" class="mt-4 flex items-center gap-2">
            @csrf
            <input name="email" type="email" placeholder="Resend verification email" class="input text-sm">
            <button type="submit" class="btn-ghost btn-sm flex-shrink-0">Resend</button>
        </form>
    @endif

    <div class="my-6 flex items-center gap-3">
        <span class="h-px flex-1 bg-slate-200"></span>
        <span class="text-xs font-semibold text-slate-400">Or</span>
        <span class="h-px flex-1 bg-slate-200"></span>
    </div>

    <p class="text-center text-sm text-slate-500">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-semibold text-brand-600 underline hover:text-brand-700">Register</a>
    </p>

    @if (\App\Models\User::where('email', 'admin@akzone.com')->exists())
        {{-- Shown only while demo data is installed; disappears once demo data is cleared. --}}
        <div class="mt-6 rounded-xl border border-dashed border-emerald-300 bg-emerald-50/70 px-4 py-3">
            <p class="flex items-center gap-1.5 text-xs font-bold text-emerald-800">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H9v1.5H7.5v1.5H6v1.5H2.25v-1.5a1.5 1.5 0 0 1 .44-1.06l5.42-5.42c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" /></svg>
                Demo credentials (for demonstration only)
            </p>
            <div class="mt-2 grid gap-2 sm:grid-cols-2">
                <button type="button"
                        onclick="document.getElementById('email').value='admin@akzone.com';document.getElementById('password').value='password';"
                        class="rounded-lg bg-white/70 px-3 py-2 text-left text-xs transition hover:bg-white">
                    <span class="block font-semibold text-emerald-700">Admin</span>
                    <span class="block font-mono text-[11px] text-slate-600">admin@akzone.com</span>
                    <span class="block font-mono text-[11px] text-slate-600">password</span>
                </button>
                <button type="button"
                        onclick="document.getElementById('email').value='user@akzone.com';document.getElementById('password').value='password';"
                        class="rounded-lg bg-white/70 px-3 py-2 text-left text-xs transition hover:bg-white">
                    <span class="block font-semibold text-emerald-700">Customer</span>
                    <span class="block font-mono text-[11px] text-slate-600">user@akzone.com</span>
                    <span class="block font-mono text-[11px] text-slate-600">password</span>
                </button>
            </div>
            <p class="mt-2 text-[11px] text-emerald-600">Click a card to auto-fill, then press Login.</p>
        </div>
    @endif
</x-guest-layout>
