<div class="card overflow-hidden">
    <div class="flex items-center justify-between border-b border-slate-100 p-5">
        <div>
            <h2 class="font-display text-lg font-bold text-ink-900">Custom pages</h2>
            <p class="text-sm text-slate-500">Editable pages with auto-generated shareable URLs.</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="btn-primary btn-sm">New page</a>
    </div>
    <div class="divide-y divide-slate-100">
        @forelse ($pages as $page)
            <div class="flex items-center justify-between gap-3 p-5">
                <div class="min-w-0">
                    <p class="font-semibold text-ink-900">{{ $page->title }}
                        @if (! $page->is_published)<span class="chip ml-1 bg-slate-100 text-slate-500 ring-1 ring-slate-200">Draft</span>@endif
                    </p>
                    <a href="{{ route('pages.show', $page) }}" target="_blank" class="text-xs text-brand-600 hover:underline">{{ url('/p/'.$page->slug) }}</a>
                </div>
                <div class="flex flex-shrink-0 items-center gap-2">
                    <a href="{{ route('admin.pages.edit', $page) }}" class="btn-ghost btn-sm">Edit</a>
                    <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('Delete this page?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="rounded-lg p-2 text-rose-500 hover:bg-rose-50" title="Delete">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="p-5 text-sm text-slate-500">No pages yet.</p>
        @endforelse
    </div>
</div>
