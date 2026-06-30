<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-ink-900 font-sans text-slate-300 antialiased">

    {{-- Navbar --}}
    <header x-data="{ open: false, scrolled: false }"
            @scroll.window="scrolled = window.pageYOffset > 10"
            class="sticky top-0 z-50 border-b border-white/5 transition-colors duration-300"
            :class="scrolled ? 'bg-ink-900/90 backdrop-blur-lg' : 'bg-transparent'">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-400 to-indigo-500 font-display text-lg font-extrabold text-ink-900 shadow-glow">A</span>
                <span class="font-display text-xl font-bold tracking-tight text-white">Akzone<span class="text-brand-400">Scripts</span></span>
            </a>

            <div class="hidden items-center gap-8 md:flex">
                <a href="{{ route('home') }}" class="text-sm font-medium text-slate-300 transition hover:text-white">Home</a>
                <a href="{{ route('products.index') }}" class="text-sm font-medium text-slate-300 transition hover:text-white">Marketplace</a>
                <a href="{{ route('products.index', ['sort' => 'popular']) }}" class="text-sm font-medium text-slate-300 transition hover:text-white">Popular</a>
                <a href="{{ route('home') }}#categories" class="text-sm font-medium text-slate-300 transition hover:text-white">Categories</a>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('cart.index') }}" class="relative rounded-lg p-2 text-slate-300 transition hover:bg-white/5 hover:text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                    @if (($cartItemCount ?? 0) > 0)
                        <span class="absolute -right-0.5 -top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-brand-400 text-xs font-bold text-ink-900">{{ $cartItemCount }}</span>
                    @endif
                </a>

                @auth
                    <div x-data="{ menu: false }" class="relative hidden md:block">
                        <button @click="menu = !menu" class="flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 py-1.5 pl-1.5 pr-3 text-sm font-medium text-white transition hover:bg-white/10">
                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-gradient-to-br from-brand-400 to-indigo-500 text-xs font-bold text-ink-900">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            {{ Str::limit(auth()->user()->name, 12) }}
                        </button>
                        <div x-show="menu" x-cloak @click.outside="menu = false" x-transition class="absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-white/10 bg-ink-800 py-1 shadow-2xl">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">Dashboard</a>
                            <a href="{{ route('dashboard.purchases') }}" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">My Purchases</a>
                            <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">Wishlist</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-300 hover:bg-white/5 hover:text-white">Settings</a>
                            @if (auth()->user()->isAdmin())
                                <div class="my-1 border-t border-white/10"></div>
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm font-medium text-brand-300 hover:bg-white/5">Admin Panel</a>
                            @endif
                            <div class="my-1 border-t border-white/10"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-rose-300 hover:bg-white/5">Log out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden text-sm font-medium text-slate-300 transition hover:text-white md:block">Sign in</a>
                    <a href="{{ route('register') }}" class="hidden rounded-lg bg-brand-400 px-4 py-2 text-sm font-semibold text-ink-900 transition hover:bg-brand-300 md:block">Get started</a>
                @endauth

                <button @click="open = !open" class="rounded-lg p-2 text-slate-300 hover:bg-white/5 md:hidden">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
            </div>
        </nav>

        {{-- Mobile menu --}}
        <div x-show="open" x-cloak x-transition class="border-t border-white/5 bg-ink-800 md:hidden">
            <div class="space-y-1 px-4 py-3">
                <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-300 hover:bg-white/5 hover:text-white">Home</a>
                <a href="{{ route('products.index') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-300 hover:bg-white/5 hover:text-white">Marketplace</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-300 hover:bg-white/5 hover:text-white">Dashboard</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-brand-300 hover:bg-white/5">Admin Panel</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-base font-medium text-rose-300 hover:bg-white/5">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-300 hover:bg-white/5 hover:text-white">Sign in</a>
                    <a href="{{ route('register') }}" class="mt-2 block rounded-lg bg-brand-400 px-3 py-2 text-center text-base font-semibold text-ink-900">Get started</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Page content --}}
    <main>
        @hasSection('hero')
            @yield('hero')
        @endif

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            @include('partials.flash')
        </div>

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-20 border-t border-white/5 bg-ink-900">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 md:grid-cols-4">
                <div class="md:col-span-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-400 to-indigo-500 font-display text-lg font-extrabold text-ink-900">A</span>
                        <span class="font-display text-xl font-bold text-white">Akzone<span class="text-brand-400">Scripts</span></span>
                    </a>
                    <p class="mt-4 max-w-sm text-sm text-slate-400">{{ config('marketplace.tagline') }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white">Marketplace</h4>
                    <ul class="mt-4 space-y-2 text-sm text-slate-400">
                        <li><a href="{{ route('products.index') }}" class="hover:text-brand-300">All products</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'popular']) }}" class="hover:text-brand-300">Popular</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'rating']) }}" class="hover:text-brand-300">Top rated</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white">Account</h4>
                    <ul class="mt-4 space-y-2 text-sm text-slate-400">
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="hover:text-brand-300">Dashboard</a></li>
                            <li><a href="{{ route('dashboard.purchases') }}" class="hover:text-brand-300">Purchases</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-brand-300">Sign in</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-brand-300">Create account</a></li>
                        @endauth
                        <li><a href="mailto:{{ config('marketplace.support_email') }}" class="hover:text-brand-300">Support</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-white/5 pt-6 sm:flex-row">
                <p class="text-sm text-slate-500">&copy; {{ date('Y') }} {{ config('marketplace.name') }}. All rights reserved.</p>
                <p class="text-sm text-slate-500">Built with Laravel {{ app()->version() }}</p>
            </div>
        </div>
    </footer>
</body>
</html>
