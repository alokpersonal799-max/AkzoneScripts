@extends('layouts.admin')

@section('page-title', 'Messages')

@section('admin')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="font-display text-2xl font-extrabold text-ink-900">Contact messages</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $unreadCount }} unread of {{ $messages->total() }} total.</p>
        </div>

        {{-- Auto-delete setting --}}
        <form method="POST" action="{{ route('admin.contacts.settings') }}" class="flex items-end gap-2">
            @csrf
            @method('PUT')
            <div>
                <label for="contact_autodelete_days" class="label">Auto-delete after (days)</label>
                <input id="contact_autodelete_days" name="contact_autodelete_days" type="number" min="0" value="{{ $autoDeleteDays }}" class="input w-44" placeholder="0 = never">
            </div>
            <button type="submit" class="btn-ghost btn-md">Save</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse ($messages as $message)
                <div class="flex items-start justify-between gap-4 p-4 transition hover:bg-slate-50 {{ $message->isRead() ? '' : 'bg-brand-50/40' }}">
                    <a href="{{ route('admin.contacts.show', $message) }}" class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            @unless ($message->isRead())<span class="h-2 w-2 flex-shrink-0 rounded-full bg-brand-500"></span>@endunless
                            <p class="truncate font-semibold text-ink-900">{{ $message->name }}</p>
                            <span class="text-xs text-slate-400">&lt;{{ $message->email }}&gt;</span>
                        </div>
                        <p class="mt-1 truncate text-sm text-slate-600">{{ $message->subject ? $message->subject.' — ' : '' }}{{ \Illuminate\Support\Str::limit($message->message, 80) }}</p>
                        <p class="mt-1 text-xs text-slate-400">{{ $message->created_at->diffForHumans() }}</p>
                    </a>
                    <form method="POST" action="{{ route('admin.contacts.destroy', $message) }}" onsubmit="return confirm('Delete this message?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Delete">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                        </button>
                    </form>
                </div>
            @empty
                <p class="p-8 text-center text-sm text-slate-400">No messages yet.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-6">{{ $messages->links() }}</div>
@endsection
