@extends('layouts.dashboard')

@section('title', $announcement->title)

@section('dashboard')
@php
    $meta = $announcement->themeMeta();
    $allowed = $announcement->allowedReplyTypes();
    $firstType = $allowed[0] ?? 'message';
    $emojis = ['👍','❤️','🎉','🔥','😍','😮','😢','👏'];
@endphp

<a href="{{ route('dashboard.inbox') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back to inbox</a>

{{-- Announcement --}}
<div class="card mt-4 overflow-hidden">
    <div class="flex items-center gap-3 border-b border-slate-100 bg-{{ $meta['color'] }}-50/60 p-5">
        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-{{ $meta['color'] }}-100 text-{{ $meta['color'] }}-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}" /></svg>
        </span>
        <div>
            <span class="rounded-full bg-{{ $meta['color'] }}-100 px-2 py-0.5 text-[11px] font-bold text-{{ $meta['color'] }}-700">{{ $meta['label'] }}</span>
            <h1 class="mt-1 font-display text-xl font-extrabold text-ink-900">{{ $announcement->title }}</h1>
            <p class="text-xs text-slate-400">{{ $announcement->sent_at?->format('M j, Y g:i A') }}</p>
        </div>
    </div>
    <div class="p-5">
        <p class="whitespace-pre-line text-sm text-slate-600">{{ $announcement->body }}</p>
        @if ($announcement->action_url)
            <a href="{{ $announcement->action_url }}" target="_blank" rel="noopener" class="btn-primary btn-sm mt-4">View details</a>
        @endif
        @if ($announcement->product)
            <a href="{{ route('products.show', $announcement->product) }}" class="btn-ghost btn-sm mt-4 border border-slate-200">See the product</a>
        @endif
    </div>
</div>

{{-- Conversation --}}
@if ($replies->isNotEmpty())
    <div class="card mt-4 p-5">
        <h2 class="text-sm font-bold text-ink-900">Conversation</h2>
        <div class="mt-4 space-y-4">
            @foreach ($replies as $r)
                <div class="flex gap-3 {{ $r->is_admin ? '' : 'flex-row-reverse' }}">
                    <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full text-xs font-bold text-white {{ $r->is_admin ? 'bg-brand-600' : 'bg-slate-400' }}">
                        {{ $r->is_admin ? 'A' : strtoupper(substr($r->user->name ?? 'U', 0, 1)) }}
                    </span>
                    <div class="max-w-[80%] rounded-xl {{ $r->is_admin ? 'bg-brand-50' : 'bg-slate-50' }} px-4 py-2.5">
                        <p class="text-xs font-semibold text-ink-900">{{ $r->is_admin ? setting('site_name', 'Store').' team' : 'You' }} <span class="font-normal text-slate-400">· {{ $r->created_at->diffForHumans() }}</span></p>
                        @if ($r->type === 'star')<p class="mt-1 text-amber-400">{!! str_repeat('★', (int) $r->rating).str_repeat('☆', 5 - (int) $r->rating) !!}</p>@endif
                        @if ($r->type === 'emoji')<p class="mt-1 text-2xl">{{ $r->emoji }}</p>@endif
                        @if ($r->type === 'media' && $r->media_url)<a href="{{ $r->media_url }}" target="_blank" rel="noopener"><img src="{{ $r->media_url }}" alt="media" class="mt-1 max-h-40 rounded-lg"></a>@endif
                        @if ($r->message)<p class="mt-1 text-sm text-slate-600">{{ $r->message }}</p>@endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Reply form --}}
@if ($announcement->allow_reply && count($allowed) > 0)
    <div class="card mt-4 p-5" x-data="{ rt: '{{ $firstType }}', rating: 0, emoji: '' }">
        <h2 class="text-sm font-bold text-ink-900">Reply</h2>

        {{-- Type tabs --}}
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach ($allowed as $t)
                <button type="button" @click="rt = '{{ $t }}'"
                        class="rounded-lg px-3 py-1.5 text-xs font-semibold transition"
                        :class="rt === '{{ $t }}' ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                    {{ ['star' => '⭐ Rating', 'emoji' => '😊 React', 'message' => '💬 Message', 'media' => '🖼️ Image'][$t] ?? $t }}
                </button>
            @endforeach
        </div>

        <form method="POST" action="{{ route('inbox.reply', $announcement) }}" enctype="multipart/form-data" class="mt-4">
            @csrf
            <input type="hidden" name="type" :value="rt">

            {{-- Star --}}
            @if (in_array('star', $allowed))
                <div x-show="rt === 'star'" class="flex items-center gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="button" @click="rating = {{ $i }}" class="focus:outline-none">
                            <svg class="h-8 w-8 transition" :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-slate-300'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z" /></svg>
                        </button>
                    @endfor
                    <input type="hidden" name="rating" :value="rating">
                </div>
            @endif

            {{-- Emoji --}}
            @if (in_array('emoji', $allowed))
                <div x-show="rt === 'emoji'" class="flex flex-wrap gap-2">
                    @foreach ($emojis as $e)
                        <button type="button" @click="emoji = '{{ $e }}'" class="rounded-lg border-2 px-2 py-1 text-2xl transition" :class="emoji === '{{ $e }}' ? 'border-brand-500 bg-brand-50' : 'border-transparent hover:bg-slate-100'">{{ $e }}</button>
                    @endforeach
                    <input type="hidden" name="emoji" :value="emoji">
                </div>
            @endif

            {{-- Message --}}
            @if (in_array('message', $allowed))
                <div x-show="rt === 'message'">
                    <textarea name="message" rows="3" class="input" placeholder="Write your reply..."></textarea>
                </div>
            @endif

            {{-- Media --}}
            @if (in_array('media', $allowed))
                <div x-show="rt === 'media'">
                    <input type="file" name="media" accept="image/*" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700">
                    <p class="mt-1 text-xs text-slate-400">Images only, up to 4 MB.</p>
                </div>
            @endif

            <button type="submit" class="btn-primary btn-md mt-4">Send reply</button>
        </form>
    </div>
@endif
@endsection
