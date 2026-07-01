<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-600 antialiased">
    <div class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="rounded-2xl bg-white p-8 shadow-soft ring-1 ring-slate-100 sm:p-10">
                {{-- Brand --}}
                <a href="{{ route('home') }}" class="flex items-center justify-center gap-2">
                    @include('partials.brand')
                </a>

                @isset($subtitle)
                    <p class="mt-3 text-center text-sm italic text-slate-400">{{ $subtitle }}</p>
                @endisset

                <div class="mt-8">
                    @include('partials.flash')
                    {{ $slot }}
                </div>
            </div>

            <p class="mt-6 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} {{ setting('site_name', config('app.name')) }}
            </p>
        </div>
    </div>
</body>
</html>
