@extends('layouts.admin')

@section('page-title', 'Telegram Connection')

@section('admin')
    <div class="mx-auto max-w-5xl space-y-8">
        <div>
            <h1 class="font-display text-2xl font-extrabold text-ink-900">Telegram Connection</h1>
            <p class="mt-1 text-sm text-slate-500">Post beautiful updates to your Telegram channels automatically. Connect one or more bots and choose what each one announces.</p>
        </div>

        {{-- How to --}}
        <div class="card border-l-4 border-l-sky-400 p-5 text-sm text-slate-600">
            <p class="font-semibold text-ink-900">Quick setup</p>
            <ol class="mt-2 list-decimal space-y-1 pl-5">
                <li>Create a bot with <b>@BotFather</b> on Telegram and copy its <b>token</b>.</li>
                <li>Add the bot to your channel/group as an <b>administrator</b> with permission to post.</li>
                <li>Paste the token and your channel as the <b>Chat ID</b> (e.g. <code class="rounded bg-slate-100 px-1">@yourchannel</code> or a numeric id like <code class="rounded bg-slate-100 px-1">-1001234567890</code>).</li>
                <li>Pick which updates this bot should send, save, then hit <b>Test</b>.</li>
            </ol>
        </div>

        {{-- Connected bots --}}
        <div>
            <h2 class="font-display text-lg font-bold text-ink-900">Connected bots</h2>
            <div class="mt-4 space-y-4">
                @forelse ($bots as $bot)
                    <div class="card p-6">
                        <form method="POST" action="{{ route('admin.tg.update', $bot) }}" class="space-y-4">
                            @csrf @method('PUT')
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-sky-50 text-sky-500">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.23 7.68-6.92c.34-.31-.07-.46-.52-.19L7.74 13.3 3.64 12c-.88-.25-.89-.86.2-1.3l15.97-6.16c.73-.33 1.43.18 1.15 1.3l-2.72 12.81c-.19.91-.74 1.13-1.5.71L12.6 16.3l-1.99 1.93c-.23.23-.42.42-.83.42z"/></svg>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-ink-900">{{ $bot->name }}</p>
                                        <p class="text-xs text-slate-400">Token: {{ $bot->masked_token }}</p>
                                    </div>
                                </div>
                                <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                                    <input type="checkbox" name="is_active" value="1" {{ $bot->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                                    Active
                                </label>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div><label class="label">Bot name</label><input name="name" type="text" value="{{ $bot->name }}" class="input"></div>
                                <div><label class="label">Chat / channel ID</label><input name="chat_id" type="text" value="{{ $bot->chat_id }}" class="input"></div>
                            </div>
                            <div>
                                <label class="label">Replace token <span class="text-slate-400">(leave blank to keep current)</span></label>
                                <input name="token" type="text" class="input" placeholder="••••••••" autocomplete="off">
                            </div>

                            <div>
                                <p class="label">Send these updates</p>
                                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                    <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                        <input type="checkbox" name="events[]" value="all" {{ in_array('all', $bot->events ?? []) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                                        <span class="font-semibold text-ink-900">All updates</span>
                                    </label>
                                    @foreach ($events as $key => $label)
                                        <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                            <input type="checkbox" name="events[]" value="{{ $key }}" {{ in_array($key, $bot->events ?? []) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                                            <span class="text-slate-600">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <p class="mt-1 text-xs text-slate-400">Tick <b>All updates</b> to send everything, or pick specific types. Untick to stop a type.</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                                <button type="submit" class="btn-primary btn-sm">Save changes</button>
                            </div>
                        </form>

                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <form method="POST" action="{{ route('admin.tg.test', $bot) }}">
                                @csrf
                                <button type="submit" class="btn-ghost btn-sm">Send test</button>
                            </form>
                            <form method="POST" action="{{ route('admin.tg.destroy', $bot) }}" onsubmit="return confirm('Remove this bot?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-xl border border-rose-200 px-3 py-1.5 text-sm font-semibold text-rose-600 hover:bg-rose-50">Remove</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="card p-8 text-center text-sm text-slate-400">No bots connected yet. Add one below.</div>
                @endforelse
            </div>
        </div>

        {{-- Add bot --}}
        <div class="card p-6">
            <h2 class="font-display text-lg font-bold text-ink-900">Add a bot</h2>
            <form method="POST" action="{{ route('admin.tg.store') }}" class="mt-4 space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><label for="name" class="label">Bot name</label><input id="name" name="name" type="text" value="{{ old('name') }}" class="input" placeholder="My channel bot"></div>
                    <div><label for="chat_id" class="label">Chat / channel ID</label><input id="chat_id" name="chat_id" type="text" value="{{ old('chat_id') }}" class="input" placeholder="@yourchannel"></div>
                </div>
                <div><label for="token" class="label">Bot token</label><input id="token" name="token" type="text" value="{{ old('token') }}" class="input" placeholder="123456:ABC-DEF..." autocomplete="off"></div>
                <div>
                    <p class="label">Send these updates</p>
                    <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                        <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                            <input type="checkbox" name="events[]" value="all" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                            <span class="font-semibold text-ink-900">All updates</span>
                        </label>
                        @foreach ($events as $key => $label)
                            <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                <input type="checkbox" name="events[]" value="{{ $key }}" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                                <span class="text-slate-600">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                    Active
                </label>
                <button type="submit" class="btn-primary btn-md">Connect bot</button>
            </form>
        </div>

        {{-- Custom broadcast --}}
        <div class="card p-6">
            <h2 class="font-display text-lg font-bold text-ink-900">Send a custom message</h2>
            <p class="mt-1 text-sm text-slate-500">Broadcast to every active bot subscribed to <b>Custom broadcast messages</b>. HTML like <code class="rounded bg-slate-100 px-1">&lt;b&gt;bold&lt;/b&gt;</code> is supported.</p>
            <form method="POST" action="{{ route('admin.tg.broadcast') }}" class="mt-4 space-y-3">
                @csrf
                <textarea name="message" rows="3" class="input" placeholder="Type your announcement...">{{ old('message') }}</textarea>
                <button type="submit" class="btn-primary btn-md">Broadcast now</button>
            </form>
        </div>

        {{-- Auto promotion --}}
        <div class="card p-6">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="font-display text-lg font-bold text-ink-900">Auto product promotion</h2>
                    <p class="mt-1 text-sm text-slate-500">Automatically recommend a random product to your channel on a schedule. Sent to bots subscribed to <b>Auto product promotion</b>.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.tg.autopromo') }}" class="mt-4 space-y-4">
                @csrf @method('PUT')
                <label class="flex items-center gap-2 text-sm font-semibold text-ink-900">
                    <input type="checkbox" name="autotgpromo_enabled" value="1" {{ old('autotgpromo_enabled', $autoEnabled) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                    Enable auto promotion
                </label>
                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <label for="autotgpromo_interval" class="label">Send every</label>
                        <input id="autotgpromo_interval" name="autotgpromo_interval" type="number" min="1" max="1000" value="{{ old('autotgpromo_interval', $autoInterval) }}" class="input w-28">
                    </div>
                    <div>
                        <label for="autotgpromo_unit" class="label">Unit</label>
                        <select id="autotgpromo_unit" name="autotgpromo_unit" class="input w-36">
                            @foreach (['minutes' => 'Minutes', 'hours' => 'Hours', 'days' => 'Days'] as $value => $label)
                                <option value="{{ $value }}" {{ old('autotgpromo_unit', $autoUnit) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary btn-md">Save schedule</button>
                </div>
                <p class="text-xs text-slate-400">Triggered by site traffic (no cron needed). A random published product is posted once the interval elapses.</p>
            </form>
        </div>

        {{-- Previews --}}
        <div>
            <h2 class="font-display text-lg font-bold text-ink-900">Message previews</h2>
            <p class="mt-1 text-sm text-slate-500">Exactly how each update looks when posted to Telegram.</p>
            @php
                $previewLabels = [
                    'registration' => 'New registration',
                    'product_added' => 'Product added',
                    'category_added' => 'Category added',
                    'promotion' => 'Promotion update',
                    'purchase' => 'Purchase',
                    'review' => 'Review',
                    'free_download' => 'Free download',
                    'custom' => 'Custom message',
                ];
            @endphp
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @foreach ($previews as $key => $msg)
                    <div>
                        <p class="mb-1.5 text-xs font-bold uppercase tracking-wide text-slate-400">{{ $previewLabels[$key] ?? $key }}</p>
                        <div class="overflow-hidden rounded-2xl rounded-tl-md border border-slate-200 bg-[#eef3f8] p-1.5 shadow-sm">
                            <div class="overflow-hidden rounded-xl bg-white">
                                @if (! empty($msg['photo']))
                                    <img src="{{ $msg['photo'] }}" alt="" class="h-36 w-full object-cover">
                                @endif
                                <div class="p-3">
                                    <p class="text-sm leading-relaxed text-ink-900">{!! nl2br($msg['text']) !!}</p>
                                    @if (! empty($msg['buttons']))
                                        <div class="mt-3 space-y-1.5">
                                            @foreach (array_chunk($msg['buttons'], 2) as $row)
                                                <div class="grid grid-cols-{{ count($row) }} gap-1.5">
                                                    @foreach ($row as $button)
                                                        <div class="rounded-lg bg-sky-50 py-1.5 text-center text-xs font-semibold text-sky-600">{{ $button[0] }}</div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
