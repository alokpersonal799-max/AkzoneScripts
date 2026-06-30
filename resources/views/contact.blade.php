@extends('layouts.app')

@section('title', 'Contact us')

@section('content')
<div class="mx-auto max-w-5xl px-4 pb-20 pt-10 sm:px-6 lg:px-8">
    <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
        {{-- Intro --}}
        <div>
            <span class="chip bg-brand-50 text-brand-700">We're here to help</span>
            <h1 class="mt-4 font-display text-4xl font-extrabold tracking-tight text-ink-900">Get in touch</h1>
            <p class="mt-4 text-slate-500">Have a question about a product, an order, or a custom request? Send us a message and our team will get back to you. You don't need an account to write to us.</p>

            <div class="mt-8 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                    </span>
                    <div>
                        <p class="text-xs text-slate-400">Email us</p>
                        <a href="mailto:{{ setting('support_email', config('marketplace.support_email')) }}" class="font-semibold text-ink-900 hover:text-brand-600">{{ setting('support_email', config('marketplace.support_email')) }}</a>
                    </div>
                </div>
                @if (setting('contact_whatsapp'))
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 0 1-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.82 11.82 0 0 1 8.413 3.488 11.82 11.82 0 0 1 3.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 0 1-5.688-1.448L.057 24Z"/></svg>
                        </span>
                        <div>
                            <p class="text-xs text-slate-400">WhatsApp</p>
                            <a href="https://wa.me/{{ setting('contact_whatsapp') }}" target="_blank" rel="noopener" class="font-semibold text-ink-900 hover:text-brand-600">Chat with us</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Form --}}
        <div class="card p-6 sm:p-8">
            @include('partials.flash')

            <form method="POST" action="{{ route('contact.store') }}" class="space-y-5">
                @csrf

                {{-- Honeypot (hidden from humans) --}}
                <div class="hidden" aria-hidden="true">
                    <label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="label">Your name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name ?? '') }}" required class="input @error('name') ring-2 ring-rose-300 @enderror">
                        @error('name')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="label">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email ?? '') }}" required class="input @error('email') ring-2 ring-rose-300 @enderror">
                        @error('email')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="subject" class="label">Subject <span class="text-slate-400">(optional)</span></label>
                    <input id="subject" name="subject" type="text" value="{{ old('subject') }}" class="input" placeholder="How can we help?">
                </div>

                <div>
                    <label for="message" class="label">Message</label>
                    <textarea id="message" name="message" rows="5" required class="input @error('message') ring-2 ring-rose-300 @enderror" placeholder="Write your message...">{{ old('message') }}</textarea>
                    @error('message')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn-primary btn-lg w-full">Send message</button>
            </form>
        </div>
    </div>
</div>
@endsection
