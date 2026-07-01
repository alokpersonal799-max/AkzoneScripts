<x-guest-layout>
    <x-slot:subtitle>Register new account</x-slot:subtitle>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="label">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="input @error('name') ring-2 ring-rose-300 @enderror" placeholder="Your name">
            @error('name')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="label">Email</label>
            <div class="relative">
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                       class="input pr-10 @error('email') ring-2 ring-rose-300 @enderror" placeholder="you@example.com">
                <svg class="pointer-events-none absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
            </div>
            @error('email')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
        </div>

        <div x-data="{ show: false }">
            <label for="password" class="label">Password</label>
            <div class="relative">
                <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="new-password"
                       class="input pr-10 @error('password') ring-2 ring-rose-300 @enderror" placeholder="••••••••">
                <button type="button" @click="show = !show" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" title="Show/hide password">
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                    <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                </button>
            </div>
            @error('password')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                   class="input" placeholder="••••••••">
        </div>

        @include('partials.captcha')

        <button type="submit" class="btn-primary btn-lg w-full">Register</button>
    </form>

    <div class="my-6 flex items-center gap-3">
        <span class="h-px flex-1 bg-slate-200"></span>
        <span class="text-xs font-semibold text-slate-400">Or</span>
        <span class="h-px flex-1 bg-slate-200"></span>
    </div>

    <p class="text-center text-sm text-slate-500">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 underline hover:text-brand-700">Login</a>
    </p>
</x-guest-layout>
