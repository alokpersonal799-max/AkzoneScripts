@extends('layouts.admin')

@section('page-title', 'New announcement')

@section('admin')
<div class="mx-auto max-w-3xl"
     x-data="{
        theme: 'custom',
        audience: 'all',
        schedule: 'now',
        allowReply: false,
        userSearch: '',
     }">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.announcements.index') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; Back</a>
        <h1 class="font-display text-2xl font-extrabold text-ink-900">New announcement</h1>
    </div>

    <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-6">
        @csrf

        {{-- Theme --}}
        <div class="card p-6">
            <h2 class="text-sm font-bold text-ink-900">1. Choose a theme</h2>
            <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach ($themes as $key => $t)
                    <label class="cursor-pointer rounded-xl border-2 p-3 transition"
                           :class="theme === '{{ $key }}' ? 'border-{{ $t['color'] }}-500 bg-{{ $t['color'] }}-50/60' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="theme" value="{{ $key }}" x-model="theme" class="sr-only">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-{{ $t['color'] }}-100 text-{{ $t['color'] }}-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $t['icon'] }}" /></svg>
                        </span>
                        <span class="mt-2 block text-sm font-semibold text-ink-900">{{ $t['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Content --}}
        <div class="card space-y-4 p-6">
            <h2 class="text-sm font-bold text-ink-900">2. Message</h2>
            <div>
                <label class="label">Title</label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="255" class="input" placeholder="e.g. Big weekend sale — 30% off!">
            </div>
            <div>
                <label class="label">Body</label>
                <textarea name="body" rows="5" required maxlength="5000" class="input" placeholder="Write your announcement...">{{ old('body') }}</textarea>
            </div>
            <div>
                <label class="label">Action link <span class="text-slate-400">(optional)</span></label>
                <input type="url" name="action_url" value="{{ old('action_url') }}" class="input" placeholder="https://... (e.g. a product or offer page)">
            </div>
        </div>

        {{-- Audience --}}
        <div class="card space-y-4 p-6">
            <h2 class="text-sm font-bold text-ink-900">3. Who receives it?</h2>
            <div class="grid gap-3 sm:grid-cols-2">
                <label class="cursor-pointer rounded-xl border-2 p-3 transition" :class="audience === 'all' ? 'border-brand-500 bg-brand-50/60' : 'border-slate-200'">
                    <input type="radio" name="audience" value="all" x-model="audience" class="sr-only">
                    <span class="block text-sm font-semibold text-ink-900">All users</span>
                    <span class="block text-xs text-slate-500">Send to everyone.</span>
                </label>
                <label class="cursor-pointer rounded-xl border-2 p-3 transition" :class="audience === 'selected' ? 'border-brand-500 bg-brand-50/60' : 'border-slate-200'">
                    <input type="radio" name="audience" value="selected" x-model="audience" class="sr-only">
                    <span class="block text-sm font-semibold text-ink-900">Selected users</span>
                    <span class="block text-xs text-slate-500">Pick one or more users.</span>
                </label>
            </div>

            <div x-show="audience === 'selected'" x-cloak>
                <input type="text" x-model="userSearch" placeholder="Search users by name or email..." class="input mb-2">
                <div class="max-h-64 space-y-1 overflow-y-auto rounded-xl border border-slate-200 p-2">
                    @forelse ($users as $u)
                        <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-slate-50"
                               x-show="userSearch === '' || @js(strtolower($u->name.' '.$u->email)).includes(userSearch.toLowerCase())">
                            <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-ink-900">{{ $u->name }}</span>
                            <span class="text-xs text-slate-400">{{ $u->email }}</span>
                        </label>
                    @empty
                        <p class="px-2 py-3 text-sm text-slate-400">No users yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Schedule --}}
        <div class="card space-y-4 p-6">
            <h2 class="text-sm font-bold text-ink-900">4. When to send?</h2>
            <div class="grid gap-3 sm:grid-cols-2">
                <label class="cursor-pointer rounded-xl border-2 p-3 transition" :class="schedule === 'now' ? 'border-brand-500 bg-brand-50/60' : 'border-slate-200'">
                    <input type="radio" name="schedule" value="now" x-model="schedule" class="sr-only">
                    <span class="block text-sm font-semibold text-ink-900">Send now</span>
                </label>
                <label class="cursor-pointer rounded-xl border-2 p-3 transition" :class="schedule === 'later' ? 'border-brand-500 bg-brand-50/60' : 'border-slate-200'">
                    <input type="radio" name="schedule" value="later" x-model="schedule" class="sr-only">
                    <span class="block text-sm font-semibold text-ink-900">Schedule</span>
                </label>
            </div>
            <div x-show="schedule === 'later'" x-cloak>
                <label class="label">Send at</label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="input">
                <p class="mt-1 text-xs text-slate-400">Requires the scheduler cron to be running (see Cron Jobs).</p>
            </div>
        </div>

        {{-- Replies --}}
        <div class="card space-y-4 p-6">
            <h2 class="text-sm font-bold text-ink-900">5. Allow replies?</h2>
            <label class="flex items-center gap-3">
                <input type="checkbox" name="allow_reply" value="1" x-model="allowReply" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                <span class="text-sm font-medium text-slate-700">Let users respond to this announcement</span>
            </label>
            <div x-show="allowReply" x-cloak>
                <p class="text-xs text-slate-500">Choose which reply formats users may use:</p>
                <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-4">
                    @foreach (['star' => 'Star rating', 'emoji' => 'Emoji react', 'message' => 'Message', 'media' => 'Image upload'] as $val => $label)
                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2">
                            <input type="checkbox" name="reply_types[]" value="{{ $val }}" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-slate-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.announcements.index') }}" class="btn-ghost btn-md">Cancel</a>
            <button type="submit" class="btn-primary btn-lg" x-text="schedule === 'now' ? 'Send announcement' : 'Schedule announcement'">Send announcement</button>
        </div>
    </form>
</div>
@endsection
