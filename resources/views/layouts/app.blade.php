<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-600 antialiased">

    {{-- Top announcement bar (managed in Admin → Promotions) --}}
    @if (setting('announcement_enabled', '0') === '1' && setting('announcement_text'))
        @php
            $annType = setting('announcement_type', 'offer');
            $annLink = setting('announcement_link') ?: route('products.index');
            $annStyles = [
                'info'    => ['bar' => 'bg-gradient-to-r from-brand-600 to-indigo-600', 'icon' => 'M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z', 'label' => 'Info', 'cta' => 'Learn more'],
                'offer'   => ['bar' => 'bg-gradient-to-r from-fuchsia-600 via-brand-600 to-indigo-600', 'icon' => 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z', 'label' => 'Offer', 'cta' => 'Shop now'],
                'success' => ['bar' => 'bg-gradient-to-r from-emerald-600 to-teal-600', 'icon' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'label' => 'Success', 'cta' => 'View'],
                'warning' => ['bar' => 'bg-gradient-to-r from-amber-500 to-orange-500', 'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z', 'label' => 'Warning', 'cta' => 'Details'],
                'alert'   => ['bar' => 'bg-gradient-to-r from-rose-600 to-red-600', 'icon' => 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0', 'label' => 'Alert', 'cta' => 'View'],
            ];
            $ann = $annStyles[$annType] ?? $annStyles['offer'];
        @endphp
        <div x-data="{ show: false, key: 'akz_ann_{{ substr(md5(setting('announcement_text').$annType.($annLink)), 0, 10) }}', init(){ this.show = localStorage.getItem(this.key) !== '1' } }" x-show="show" x-cloak class="relative {{ $ann['bar'] }} text-white">
            <div class="mx-auto flex max-w-7xl items-center justify-center gap-2.5 px-10 py-2 text-center text-xs font-medium sm:text-sm">
                <svg class="hidden h-4 w-4 flex-shrink-0 sm:inline-flex" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ann['icon'] }}" /></svg>
                <span class="hidden rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide sm:inline-flex">{{ $ann['label'] }}</span>
                <span>{{ setting('announcement_text') }}</span>
                <a href="{{ $annLink }}" class="ml-1 inline-flex items-center gap-1 font-bold text-white underline-offset-2 hover:underline">
                    {{ $ann['cta'] }}
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                </a>
            </div>
            <button type="button" @click="show = false; localStorage.setItem(key, '1')" title="Dismiss"
                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full p-1 text-white/70 transition hover:bg-white/20 hover:text-white">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
        </div>
    @endif

    {{-- Navbar --}}
    <header x-data="{ open: false, scrolled: false }"
            @scroll.window="scrolled = window.pageYOffset > 8"
            class="sticky top-0 z-50 border-b transition-all duration-300"
            :class="scrolled ? 'border-slate-200 bg-white/90 backdrop-blur-lg shadow-sm' : 'border-transparent bg-white'">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex flex-shrink-0 items-center gap-2">
                @if (setting('site_logo'))
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(setting('site_logo')) }}" alt="{{ setting('site_name', 'AkzoneScripts') }}" class="h-9 w-auto">
                @else
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-indigo-500 font-display text-lg font-extrabold text-white shadow-lift">{{ strtoupper(substr(setting('site_name', 'A'), 0, 1)) }}</span>
                    <span class="font-display text-xl font-extrabold tracking-tight text-ink-900">{{ setting('site_name', 'AkzoneScripts') }}</span>
                @endif
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
                {{-- Currency switcher --}}
                @if (($activeCurrencies ?? collect())->count() > 1)
                    <div x-data="{ open: false }" class="relative hidden sm:block">
                        <button @click="open = !open" class="flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-ink-900 transition hover:bg-slate-50">
                            {{ $currentCurrency?->code ?? 'USD' }}
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1.5 shadow-soft">
                            @foreach ($activeCurrencies as $currency)
                                <a href="{{ route('currency.switch', $currency->code) }}" class="flex items-center justify-between px-4 py-2 text-sm hover:bg-slate-50 {{ ($currentCurrency?->code === $currency->code) ? 'font-bold text-brand-600' : 'text-slate-600' }}">
                                    <span>{{ $currency->code }} · {{ $currency->name }}</span>
                                    <span>{{ $currency->symbol }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

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
        @if (setting('contact_telegram'))
            <a href="https://t.me/{{ setting('contact_telegram') }}" target="_blank" rel="noopener" title="Message us on Telegram"
               class="flex h-11 w-11 items-center justify-center rounded-full bg-sky-500 text-white shadow-lift transition hover:scale-110">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/></svg>
            </a>
        @endif
        @if (setting('contact_whatsapp'))
            <a href="https://wa.me/{{ setting('contact_whatsapp') }}" target="_blank" rel="noopener" title="Chat on WhatsApp"
               class="flex h-11 w-11 items-center justify-center rounded-full bg-emerald-500 text-white shadow-lift transition hover:scale-110">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.82 11.82 0 0 1 8.413 3.488 11.82 11.82 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24Zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 0 0 1.51 5.26l-.999 3.648 3.737-.98 .242 .147Z"/></svg>
            </a>
        @endif
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
            <div class="grid gap-10 md:grid-cols-12">
                <div class="md:col-span-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        @include('partials.brand')
                    </a>
                    <p class="mt-4 max-w-sm text-sm text-slate-500">{{ setting('footer_about', config('marketplace.tagline')) }}</p>
                    <div class="mt-5 flex flex-wrap gap-2">
                        <a href="mailto:{{ setting('support_email', config('marketplace.support_email')) }}" title="Email support" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition hover:bg-brand-600 hover:text-white">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                        </a>
                        @php
                            $socialIcons = [
                                'twitter' => 'M22 5.92c-.74.33-1.53.55-2.36.65a4.12 4.12 0 0 0 1.8-2.27c-.79.47-1.67.81-2.6 1a4.1 4.1 0 0 0-7 3.74A11.65 11.65 0 0 1 3.39 4.6a4.1 4.1 0 0 0 1.27 5.47c-.66-.02-1.28-.2-1.82-.5v.05a4.1 4.1 0 0 0 3.29 4.02c-.3.08-.62.13-.95.13-.23 0-.46-.02-.68-.06a4.1 4.1 0 0 0 3.83 2.85A8.23 8.23 0 0 1 2 18.29a11.62 11.62 0 0 0 6.29 1.84c7.55 0 11.68-6.25 11.68-11.67l-.01-.53A8.3 8.3 0 0 0 22 5.92Z',
                                'github' => 'M12 2a10 10 0 0 0-3.16 19.49c.5.09.68-.22.68-.48l-.01-1.7c-2.78.6-3.37-1.34-3.37-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.9 1.53 2.36 1.09 2.94.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.94 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.65 0 0 .84-.27 2.75 1.02a9.56 9.56 0 0 1 5 0c1.91-1.29 2.75-1.02 2.75-1.02.55 1.38.2 2.4.1 2.65.64.7 1.03 1.59 1.03 2.68 0 3.84-2.34 4.69-4.57 4.94.36.31.68.92.68 1.85l-.01 2.74c0 .27.18.58.69.48A10 10 0 0 0 12 2Z',
                                'discord' => 'M20 4.4A19.8 19.8 0 0 0 15.1 3l-.25.5a18.3 18.3 0 0 0-5.7 0L8.9 3A19.8 19.8 0 0 0 4 4.4 20.4 20.4 0 0 0 .5 18.2 19.9 19.9 0 0 0 6.6 21l.5-.7c-1-.4-1.9-.9-2.7-1.5l.6-.5c2.4 1.1 5 1.7 7.6 1.7 2.6 0 5.2-.6 7.6-1.7l.6.5c-.8.6-1.7 1.1-2.7 1.5l.5.7a19.9 19.9 0 0 0 6.1-2.8A20.4 20.4 0 0 0 20 4.4ZM8.5 15c-.9 0-1.7-.9-1.7-2s.8-2 1.7-2 1.7.9 1.7 2-.8 2-1.7 2Zm7 0c-.9 0-1.7-.9-1.7-2s.8-2 1.7-2 1.7.9 1.7 2-.8 2-1.7 2Z',
                                'facebook' => 'M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.77l-.44 2.89h-2.33v6.99A10 10 0 0 0 22 12Z',
                            ];
                        @endphp
                        @foreach ($socialIcons as $social => $path)
                            @if (setting('social_'.$social) && setting('social_'.$social) !== '#')
                                <a href="{{ setting('social_'.$social) }}" target="_blank" rel="noopener" title="{{ ucfirst($social) }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500 transition hover:bg-brand-600 hover:text-white">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $path }}" /></svg>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="md:col-span-3">
                    <h4 class="text-sm font-bold text-ink-900">Categories</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-500">
                        <li><a href="{{ route('products.index') }}" class="hover:text-brand-600">All products</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'popular']) }}" class="hover:text-brand-600">Best selling</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'rating']) }}" class="hover:text-brand-600">Top rated</a></li>
                        <li><a href="{{ route('products.index') }}#categories" class="hover:text-brand-600">Browse all</a></li>
                    </ul>
                </div>
                <div class="md:col-span-3">
                    <h4 class="text-sm font-bold text-ink-900">Quick Links</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-500">
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="hover:text-brand-600">Dashboard</a></li>
                            <li><a href="{{ route('dashboard.purchases') }}" class="hover:text-brand-600">My purchases</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-brand-600">Sign in</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-brand-600">Create account</a></li>
                        @endauth
                        <li><a href="{{ route('cart.index') }}" class="hover:text-brand-600">Cart</a></li>
                        <li><a href="{{ route('contact.show') }}" class="hover:text-brand-600">Contact us</a></li>
                        <li><a href="mailto:{{ config('marketplace.support_email') }}" class="hover:text-brand-600">Support</a></li>
                    </ul>
                </div>
                <div class="md:col-span-2">
                    <h4 class="text-sm font-bold text-ink-900">Legal</h4>
                    <ul class="mt-4 space-y-2.5 text-sm text-slate-500">
                        @forelse (\App\Models\Page::footerLinks() as $page)
                            <li><a href="{{ route('pages.show', $page) }}" class="hover:text-brand-600">{{ $page->title }}</a></li>
                        @empty
                            <li><a href="#" class="hover:text-brand-600">Terms of service</a></li>
                            <li><a href="#" class="hover:text-brand-600">Privacy policy</a></li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Secure payments --}}
            <div class="mt-12 flex flex-col items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 sm:flex-row">
                <div class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                    <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                    Secure payments
                </div>
                <div class="flex flex-wrap items-center justify-center gap-2">
                    @foreach (['VISA', 'Mastercard', 'PayPal', 'Stripe', 'Bitcoin', 'Ethereum', 'USDT'] as $pay)
                        <span class="rounded-md border border-slate-200 bg-white px-2.5 py-1 text-[10px] font-bold text-slate-500">{{ $pay }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Trust badges --}}
            <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-5">
                @php
                    $trust = [
                        ['SSL Secured', 'M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z'],
                        ['Instant Delivery', 'm3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z'],
                        ['24/7 Support', 'M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z'],
                        ['Money-Back', 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                        ['Verified Products', 'm9 12.75 3 3m0 0 3-3m-3 3v-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                    ];
                @endphp
                @foreach ($trust as [$label, $icon])
                    <div class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-center">
                        <svg class="h-4 w-4 flex-shrink-0 text-brand-500" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" /></svg>
                        <span class="text-xs font-semibold text-slate-600">{{ $label }}</span>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex flex-col items-center justify-between gap-4 border-t border-slate-100 pt-6 sm:flex-row">
                <p class="text-sm text-slate-400">{{ setting('footer_copyright', '© '.date('Y').' '.config('marketplace.name').'. All rights reserved.') }}</p>
                <p class="text-sm text-slate-400">Crafted with care for modern builders.</p>
            </div>
        </div>
    </footer>

    {{-- Tawk.to live chat --}}
    @if (setting('tawk_enabled') === '1' && setting('tawk_embed'))
        {!! setting('tawk_embed') !!}
    @endif

    {{-- Promotional Popup --}}
    @if (setting('popup_enabled', '0') === '1')
        @include('partials.popup')
    @endif
</body>
</html>
