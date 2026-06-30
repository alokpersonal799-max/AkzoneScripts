<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-600 antialiased">

    {{-- Navbar --}}
    <header x-data="{ open: false, scrolled: false }"
            @scroll.window="scrolled = window.pageYOffset > 8"
            class="sticky top-0 z-50 border-b transition-all duration-300"
            :class="scrolled ? 'border-slate-200 bg-white/90 backdrop-blur-lg shadow-sm' : 'border-transparent bg-white'">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex flex-shrink-0 items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-indigo-500 font-display text-lg font-extrabold text-white shadow-lift">A</span>
                <span class="font-display text-xl font-extrabold tracking-tight text-ink-900">Akzone<span class="text-brand-600">Scripts</span></span>
            </a>

            {{-- Center search (desktop) --}}
            <form action="{{ route('products.index') }}" method="GET" class="hidden flex-1 items-center lg:flex">
                <div class="relative mx-auto w-full max-w-md">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                    <input type="text" name="q" placeholder="Search products..." class="w-full rounded-full border border-slate-200 bg-slate-50 py-2.5 pl-11 pr-4 text-sm text-slate-700 placeholder-slate-400 focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-500/10">
                </div>
            </form>

            <div class="hidden items-center gap-1 md:flex">
                <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:text-ink-900">Home</a>
                <a href="{{ route('products.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:text-ink-900">Marketplace</a>
                <a href="{{ route('home') }}#categories" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 transition hover:text-ink-900">Categories</a>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('cart.index') }}" class="relative rounded-xl p-2.5 text-slate-600 transition hover:bg-slate-100 hover:text-ink-900">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
                    @if (($cartItemCount ?? 0) > 0)
                        <span class="absolute -right-0.5 -top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white">{{ $cartItemCount }}</span>
                    @endif
                </a>

                @auth
                    <div x-data="{ menu: false }" class="relative hidden md:block">
                        <button @click="menu = !menu" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white py-1.5 pl-1.5 pr-3 text-sm font-semibold text-ink-900 transition hover:bg-slate-50">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-br from-brand-500 to-indigo-500 text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            {{ Str::limit(auth()->user()->name, 10) }}
                        </button>
                        <div x-show="menu" x-cloak @click.outside="menu = false" x-transition class="absolute right-0 mt-2 w-52 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1.5 shadow-soft">
                            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-ink-900">Dashboard</a>
                            <a href="{{ route('dashboard.purchases') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-ink-900">My Purchases</a>
                            <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-ink-900">Wishlist</a>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-ink-900">Settings</a>
                            @if (auth()->user()->isAdmin())
                                <div class="my-1 border-t border-slate-100"></div>
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm font-semibold text-brand-600 hover:bg-slate-50">Admin Panel</a>
                            @endif
                            <div class="my-1 border-t border-slate-100"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-rose-600 hover:bg-slate-50">Log out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:text-ink-900 md:block">Sign in</a>
                    <a href="{{ route('register') }}" class="btn-primary btn-md hidden md:inline-flex">Get started</a>
                @endauth

                <button @click="open = !open" class="rounded-xl p-2.5 text-slate-600 hover:bg-slate-100 md:hidden">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
            </div>
        </nav>

        {{-- Mobile menu --}}
        <div x-show="open" x-cloak x-transition class="border-t border-slate-100 bg-white md:hidden">
            <div class="space-y-1 px-4 py-3">
                <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50">Home</a>
                <a href="{{ route('products.index') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50">Marketplace</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50">Dashboard</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-base font-semibold text-brand-600 hover:bg-slate-50">Admin Panel</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-base font-medium text-rose-600 hover:bg-slate-50">Log out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50">Sign in</a>
                    <a href="{{ route('register') }}" class="btn-primary btn-md mt-2 w-full">Get started</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Floating contact buttons --}}
    <div class="fixed left-4 top-1/2 z-40 hidden -translate-y-1/2 flex-col gap-3 lg:flex">
        <a href="{{ config('marketplace.social.discord', '#') }}" target="_blank" rel="noopener" title="Join our community"
           class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-600 text-white shadow-lift transition hover:scale-110">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A11.944 11.944 0 0 0 0 11.944C0 18.54 5.404 24 11.944 24c6.6 0 12.056-5.46 12.056-12.056C24 5.404 18.54 0 11.944 0Zm.056 4.5c1.49 0 2.852.5 3.94 1.34l-.74.74A6.4 6.4 0 0 0 12 5.5a6.5 6.5 0 1 0 6.5 6.5h1A7.5 7.5 0 1 1 12 4.5Z"/></svg>
        </a>
        <a href="https://wa.me/" target="_blank" rel="noopener" title="Chat on WhatsApp"
           class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lift transition hover:scale-110">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.82 11.82 0 0 1 8.413 3.488 11.82 11.82 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24Zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 0 0 1.51 5.26l-.999 3.648 3.737-.98 .242 .147Z"/></svg>
        </a>
    </div>

    {{-- Page content --}}
    <main>
        @yield('hero')

        @hasSection('flash-wrap')
        @else
            <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
                @include('partials.flash')
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="mt-24 border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-4">
                <div class="md:col-span-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-indigo-500 font-display text-lg font-extrabold text-white">A</span>
                        <span class="font-display text-xl font-extrabold text-ink-900">Akzone<span class="text-brand-600">Scripts</span></span>
                    </a>
                    <p class="mt-4 max-w-sm text-sm text-slate-500">{{ config('marketplace.tagline') }}</p>
                    <div class="mt-5 flex gap-2">
                        @foreach (['twitter', 'github', 'discord'] as $social)
                            <a href="{{ config('marketplace.social.'.$social, '#') }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition hover:bg-brand-600 hover:text-white">
                                <span class="text-xs font-bold uppercase">{{ substr($social, 0, 1) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-ink-900">Marketplace</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-500">
                        <li><a href="{{ route('products.index') }}" class="hover:text-brand-600">All products</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'popular']) }}" class="hover:text-brand-600">Best selling</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'rating']) }}" class="hover:text-brand-600">Top rated</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-ink-900">Account</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-500">
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="hover:text-brand-600">Dashboard</a></li>
                            <li><a href="{{ route('dashboard.purchases') }}" class="hover:text-brand-600">Purchases</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-brand-600">Sign in</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-brand-600">Create account</a></li>
                        @endauth
                        <li><a href="mailto:{{ config('marketplace.support_email') }}" class="hover:text-brand-600">Support</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-slate-100 pt-6 sm:flex-row">
                <p class="text-sm text-slate-400">&copy; {{ date('Y') }} {{ config('marketplace.name') }}. All rights reserved.</p>
                <div class="flex items-center gap-2 opacity-80">
                    @foreach (['VISA', 'MC', 'PayPal', 'Stripe'] as $pay)
                        <span class="rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-[10px] font-bold text-slate-500">{{ $pay }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
