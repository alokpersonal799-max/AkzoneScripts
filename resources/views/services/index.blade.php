@extends('layouts.app')

@section('title', 'Services')

@section('content')
<div class="mx-auto max-w-6xl px-4 pb-20 pt-10 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="text-center">
        <span class="chip bg-brand-50 text-brand-700">What we offer</span>
        <h1 class="mt-4 font-display text-4xl font-extrabold tracking-tight text-ink-900">Our Services</h1>
        <p class="mx-auto mt-3 max-w-2xl text-slate-500">Explore the services we and our trusted providers offer. Reach out through your preferred channel or send a quick inquiry.</p>
        @if (setting('portfolio_url'))
            <a href="{{ setting('portfolio_url') }}" target="_blank" rel="noopener" class="btn-primary btn-md mt-5 inline-flex">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" /></svg>
                View Portfolio
            </a>
        @endif
    </div>

    @include('partials.flash')

    {{-- Services grid: 1 column on mobile, 2 on desktop --}}
    <div class="mt-10 grid gap-6 md:grid-cols-2">
        @forelse ($services as $service)
            <div class="card flex flex-col p-6" x-data="{ inquiry: false }">
                {{-- Provider --}}
                <div class="flex items-center gap-3">
                    <img src="{{ $service->avatar_url }}" alt="{{ $service->provider_label }}" class="h-12 w-12 flex-shrink-0 rounded-xl object-cover">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-ink-900">{{ $service->provider_label }}</p>
                        <p class="text-xs text-slate-400">{{ $service->provider_type === 'custom' ? 'Provider' : 'Official' }}</p>
                    </div>
                </div>

                {{-- Body --}}
                <h2 class="mt-4 font-display text-xl font-extrabold text-ink-900">{{ $service->name }}</h2>
                @if ($service->subtitle)
                    <p class="mt-1 text-sm font-medium text-brand-600">{{ $service->subtitle }}</p>
                @endif
                @if ($service->description)
                    <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-500">{{ $service->description }}</p>
                @endif

                {{-- Contact buttons --}}
                <div class="mt-auto space-y-3 pt-5">
                    @if (! empty($service->contact_links))
                        <div class="flex flex-wrap gap-2">
                            @foreach ($service->contact_links as $link)
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white transition {{ $link['color'] }}">{{ $link['label'] }}</a>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex flex-col gap-2 sm:flex-row">
                        @if ($service->custom_url && $service->custom_label)
                            <a href="{{ $service->custom_url }}" target="_blank" rel="noopener" class="btn-primary btn-md flex-1 justify-center ring-2 ring-brand-300 ring-offset-2">✨ {{ $service->custom_label }}</a>
                        @endif
                        @if ($service->allow_inquiry)
                            <button type="button" @click="inquiry = true" class="btn-dark btn-md flex-1 justify-center">Send message</button>
                        @endif
                    </div>
                </div>

                {{-- Inquiry modal --}}
                @if ($service->allow_inquiry)
                    <div x-show="inquiry" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
                        <div class="absolute inset-0 bg-ink-900/60" @click="inquiry = false"></div>
                        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-display text-lg font-bold text-ink-900">Inquiry</h3>
                                    <p class="text-xs text-slate-400">About: {{ $service->name }}</p>
                                </div>
                                <button type="button" @click="inquiry = false" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                            <form method="POST" action="{{ route('services.inquiry', $service) }}" class="mt-4 space-y-3">
                                @csrf
                                <div class="hidden" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
                                <input name="name" type="text" required class="input" placeholder="Your name" value="{{ auth()->user()->name ?? '' }}">
                                <input name="email" type="email" required class="input" placeholder="Your email" value="{{ auth()->user()->email ?? '' }}">
                                <textarea name="message" rows="4" required class="input" placeholder="How can we help with {{ $service->name }}?"></textarea>
                                <button type="submit" class="btn-primary btn-md w-full">Send inquiry</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full">
                <x-empty-state title="No services yet" message="Services will appear here once they are added." />
            </div>
        @endforelse
    </div>
</div>

@include('partials.ads', ['page' => 'services'])
@endsection
