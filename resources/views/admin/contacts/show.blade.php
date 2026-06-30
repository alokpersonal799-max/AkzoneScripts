@extends('layouts.admin')

@section('page-title', 'Message')

@section('admin')
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.contacts.index') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to messages</a>
        </div>

        <div class="card p-6 sm:p-8">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-indigo-500 text-lg font-bold text-white">{{ strtoupper(substr($message->name, 0, 1)) }}</span>
                    <div>
                        <p class="font-display text-lg font-bold text-ink-900">{{ $message->name }}</p>
                        <a href="mailto:{{ $message->email }}" class="text-sm text-brand-600 hover:underline">{{ $message->email }}</a>
                    </div>
                </div>
                <p class="text-right text-xs text-slate-400">{{ $message->created_at->format('M j, Y g:i A') }}<br>{{ $message->created_at->diffForHumans() }}</p>
            </div>

            @if ($message->subject)
                <h2 class="mt-5 font-display text-xl font-bold text-ink-900">{{ $message->subject }}</h2>
            @endif

            <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-600">{{ $message->message }}</p>

            @if ($message->ip)
                <p class="mt-5 text-xs text-slate-400">Sent from IP: {{ $message->ip }}</p>
            @endif

            <div class="mt-6 flex items-center gap-3 border-t border-slate-100 pt-5">
                <a href="mailto:{{ $message->email }}?subject={{ rawurlencode('Re: '.($message->subject ?: 'Your message')) }}" class="btn-primary btn-md">Reply by email</a>
                <form method="POST" action="{{ route('admin.contacts.destroy', $message) }}" onsubmit="return confirm('Delete this message?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="rounded-xl border border-rose-200 px-4 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-50">Delete</button>
                </form>
            </div>
        </div>
    </div>
@endsection
