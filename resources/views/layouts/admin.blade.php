<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-600 antialiased" x-data="{ sidebar: false, collapsed: (localStorage.getItem('adminSidebarCollapsed') === '1'), toggleCollapse(){ this.collapsed = !this.collapsed; localStorage.setItem('adminSidebarCollapsed', this.collapsed ? '1' : '0'); } }">

@include('partials.page-loader')

@php
    $adminNav = [
        ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z'],
        ['route' => 'admin.products.index', 'match' => 'admin.products.*', 'label' => 'Products', 'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z'],
        ['route' => 'admin.services.index', 'match' => 'admin.services.*', 'label' => 'Services', 'icon' => 'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437'],
        ['route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'label' => 'Categories', 'icon' => 'M6 6.878V6a2.25 2.25 0 0 1 2.25-2.25h7.5A2.25 2.25 0 0 1 18 6v.878m-12 0c.235-.083.487-.128.75-.128h10.5c.263 0 .515.045.75.128m-12 0A2.25 2.25 0 0 0 4.5 9v.878m13.5-3A2.25 2.25 0 0 1 19.5 9v.878m0 0a2.246 2.246 0 0 0-.75-.128H5.25c-.263 0-.515.045-.75.128m15 0A2.25 2.25 0 0 1 21 12v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6c0-.98.626-1.813 1.5-2.122'],
        ['route' => 'admin.orders.index', 'match' => 'admin.orders.*', 'label' => 'Orders', 'icon' => 'M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z'],
        ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'label' => 'Customers', 'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z'],
        ['route' => 'admin.coupons.index', 'match' => 'admin.coupons.*', 'label' => 'Coupons', 'icon' => 'M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z'],
        ['route' => 'admin.reviews.index', 'match' => 'admin.reviews.*', 'label' => 'Reviews', 'icon' => 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z'],
        ['route' => 'admin.announcements.index', 'match' => 'admin.announcements.*', 'label' => 'Announcements', 'icon' => 'M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125'],
        ['route' => 'admin.tickets.index', 'match' => 'admin.tickets.*', 'label' => 'Support', 'icon' => 'M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z'],
        ['route' => 'admin.reports.index', 'match' => 'admin.reports.*', 'label' => 'Reports', 'icon' => 'M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5'],
        ['route' => 'admin.contacts.index', 'match' => 'admin.contacts.*', 'label' => 'Messages', 'icon' => 'M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z'],
        ['route' => 'admin.tg.index', 'match' => 'admin.tg.*', 'label' => 'TG Connection', 'icon' => 'M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5'],
        ['route' => 'admin.currencies.index', 'url' => route('admin.settings.show', 'currencies'), 'match' => 'admin.currencies.*', 'label' => 'Currencies', 'icon' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        ['route' => 'admin.storage.index', 'match' => 'admin.storage.*', 'label' => 'Storage', 'icon' => 'M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z'],
        ['route' => 'admin.promotions.index', 'match' => 'admin.promotions.*', 'label' => 'Promotions', 'icon' => 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z M6 6h.008v.008H6V6Z'],
        ['route' => 'admin.ads.index', 'match' => 'admin.ads.*', 'label' => 'Advertisement', 'icon' => 'M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46'],
        ['route' => 'admin.theme.index', 'match' => 'admin.theme.*', 'label' => 'Theme', 'icon' => 'M4.098 19.902a3.75 3.75 0 0 0 5.304 0l6.401-6.402M6.75 21A3.75 3.75 0 0 1 3 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 0 0 3.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.588 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008Z'],
        ['route' => 'admin.settings.index', 'match' => 'admin.settings.*', 'label' => 'Settings', 'icon' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z'],
        ['route' => 'admin.system.index', 'match' => 'admin.system.*', 'label' => 'System Information', 'icon' => 'M11.25 11.25l.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z'],
        ['route' => 'admin.activity.index', 'match' => 'admin.activity.*', 'label' => 'Activity Log', 'icon' => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z'],
        ['route' => 'admin.cron.index', 'match' => 'admin.cron.*', 'label' => 'Cron Jobs', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    ];
@endphp

<div class="flex min-h-screen">
    <style>
        /* Sidebar collapse only affects large screens; mobile drawer stays full. */
        @media (min-width: 1024px) {
            aside.is-collapsed { width: 5rem !important; }
            aside.is-collapsed .nav-label { display: none !important; }
            aside.is-collapsed .nav-link { justify-content: center; }
            .admin-main.is-collapsed { padding-left: 5rem !important; }
        }
    </style>
    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 flex w-64 transform flex-col border-r border-slate-200 bg-white transition-all duration-200 lg:translate-x-0"
           :class="(sidebar ? 'translate-x-0 ' : '-translate-x-full lg:translate-x-0 ') + (collapsed ? 'is-collapsed' : '')">
        <div class="flex h-16 flex-shrink-0 items-center gap-2 border-b border-slate-100 px-4">
            <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-brand-500 to-indigo-500 font-display font-extrabold text-white">A</span>
            <span class="nav-label font-display text-lg font-extrabold text-ink-900">Admin</span>
            <button type="button" @click="toggleCollapse()" class="nav-label ml-auto hidden rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-ink-900 lg:block" title="Collapse sidebar">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            {{-- Expand button shown only when collapsed (lg) --}}
            <button type="button" @click="toggleCollapse()" x-show="collapsed" class="mx-auto hidden rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-ink-900 lg:block" title="Expand sidebar" style="display:none;">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
            </button>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto p-3">
            @foreach ($adminNav as $item)
                @php $active = request()->routeIs($item['match']); @endphp
                <a href="{{ $item['url'] ?? route($item['route']) }}" title="{{ $item['label'] }}"
                   class="nav-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-50 hover:text-ink-900' }}">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                    <span class="nav-label">{{ $item['label'] }}</span>
                </a>
            @endforeach

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="pt-1">
                @csrf
                <button type="submit" title="Log out"
                        class="nav-link flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-50">
                    <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" /></svg>
                    <span class="nav-label">Log out</span>
                </button>
            </form>
        </nav>

        {{-- Credit (demo mode only) --}}
        @if (setting('demo_mode', '0') === '1')
        <div class="nav-label flex-shrink-0 border-t border-slate-100 p-4">
            <a href="https://instagram.com/i_am2_black" target="_blank" rel="noopener"
               class="block rounded-xl bg-gradient-to-br from-brand-500 to-indigo-500 p-3 text-center text-white shadow-soft transition hover:opacity-95">
                <p class="text-[11px] font-medium uppercase tracking-wide text-white/80">Platform crafted by</p>
                <p class="mt-0.5 flex items-center justify-center gap-1.5 font-display text-sm font-bold">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                    @i_am2_black
                </p>
                <p class="mt-1 text-[11px] text-white/80">Custom PHP scripts &middot; DM for orders</p>
            </a>
        </div>
        @endif
    </aside>

    {{-- Overlay for mobile --}}
    <div x-show="sidebar" x-cloak @click="sidebar = false" class="fixed inset-0 z-40 bg-slate-900/30 lg:hidden"></div>

    {{-- Main --}}
    <div class="admin-main flex flex-1 flex-col transition-all duration-200 lg:pl-64" :class="collapsed ? 'is-collapsed' : ''">
        <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6">
            <button @click="sidebar = !sidebar" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            <h1 class="font-display text-lg font-bold text-ink-900">@yield('page-title', 'Dashboard')</h1>
            <div class="ml-auto flex items-center gap-3">
                {{-- Notifications bell --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="relative flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 hover:text-brand-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                        @if (($adminNotifications ?? collect())->count() > 0)
                            <span class="absolute -right-1 -top-1 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-rose-500 px-1 text-xs font-bold text-white">{{ $adminNotifications->count() }}</span>
                        @endif
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false" x-transition class="absolute right-0 mt-2 w-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft">
                        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                            <span class="text-sm font-bold text-ink-900">Notifications</span>
                            @if (($adminNotifications ?? collect())->count() > 0)
                                <form method="POST" action="{{ route('admin.notifications.readAll') }}">@csrf<button class="text-xs font-semibold text-brand-600 hover:underline">Mark all read</button></form>
                            @endif
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                            @forelse ($adminNotifications ?? [] as $n)
                                <a href="{{ route('admin.notifications.read', $n) }}" class="block px-4 py-3 transition hover:bg-slate-50">
                                    <p class="text-sm font-semibold text-ink-900">{{ $n->title }}</p>
                                    @if ($n->body)<p class="mt-0.5 truncate text-xs text-slate-500">{{ $n->body }}</p>@endif
                                    <p class="mt-0.5 text-[11px] text-slate-400">{{ $n->created_at->diffForHumans() }}</p>
                                </a>
                            @empty
                                <p class="px-4 py-8 text-center text-sm text-slate-400">You're all caught up 🎉</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Refresh --}}
                <button type="button" x-data="{ spin: false }" @click="spin = true; window.location.reload()" title="Refresh" class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 hover:text-brand-600">
                    <svg class="h-5 w-5" :class="spin && 'animate-spin'" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                </button>

                <a href="{{ route('home') }}" target="_blank" rel="noopener" title="View storefront" class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 hover:text-brand-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m-9 9h18" /></svg>
                </a>
                <div x-data="{ menu: false }" class="relative">
                    <button @click="menu = !menu" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white py-1.5 pl-1.5 pr-3 text-sm font-semibold text-ink-900 hover:bg-slate-50">
                        <span class="flex h-7 w-7 items-center justify-center rounded-md bg-gradient-to-br from-brand-500 to-indigo-500 text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <span class="hidden sm:block">{{ Str::limit(auth()->user()->name, 14) }}</span>
                    </button>
                    <div x-show="menu" x-cloak @click.outside="menu = false" x-transition class="absolute right-0 mt-2 w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1.5 shadow-soft">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">My account</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-rose-600 hover:bg-slate-50">Log out</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @include('partials.flash')
            @yield('admin')
        </main>

        {{-- Footer credit --}}
        <footer class="border-t border-slate-200 bg-white px-4 py-5 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-2 text-center sm:flex-row sm:text-left">
                <p class="text-sm text-slate-500">
                    &copy; {{ date('Y') }} {{ setting('site_name', config('app.name')) }}. All rights reserved.
                </p>
                @if (setting('demo_mode', '0') === '1')
                <p class="flex items-center gap-1.5 text-sm text-slate-500">
                    This complete PHP script platform was created by
                    <a href="https://instagram.com/i_am2_black" target="_blank" rel="noopener" class="inline-flex items-center gap-1 font-semibold text-brand-600 hover:text-brand-700">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
                        @i_am2_black
                    </a>
                    &middot; feel free to contact for orders.
                </p>
                @endif
            </div>
        </footer>
    </div>
</div>
</body>
</html>
