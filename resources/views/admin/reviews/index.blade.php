@extends('layouts.admin')

@section('page-title', 'Reviews')

@section('admin')
    {{-- Stats --}}
    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        @foreach ([
            ['Total reviews', $stats['total'], 'slate'],
            ['Verified (genuine)', $stats['verified'], 'emerald'],
            ['Free product', $stats['free'], 'cyan'],
            ['Approved', $stats['approved'], 'brand'],
            ['Pending', $stats['pending'], 'amber'],
            ['Testimonials', $stats['testimonials'], 'indigo'],
        ] as $s)
            <div class="card p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $s[0] }}</p>
                <p class="mt-1 font-display text-2xl font-extrabold text-{{ $s[2] }}-600">{{ number_format($s[1]) }}</p>
            </div>
        @endforeach
    </div>

    <div x-data="{ selected: [], allIds: @js($reviews->pluck('id')->values()) }">
    <div class="mb-6 flex flex-wrap items-center gap-2">
        @foreach (['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'testimonials' => 'Testimonials'] as $key => $label)
            <a href="{{ route('admin.reviews.index', $key ? ['filter' => $key] : []) }}"
               class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ $filter === $key ? 'bg-brand-600 text-white' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                {{ $label }}@if ($key === 'pending' && $pendingCount) <span class="ml-1 rounded-full bg-amber-400 px-1.5 text-xs text-white">{{ $pendingCount }}</span>@endif
            </a>
        @endforeach

        <div class="ml-auto flex items-center gap-3">
            <button type="button" @click="selected = allIds.slice(0, 10)" class="text-sm font-semibold text-brand-600 hover:text-brand-700">Select 10</button>
            <label class="flex items-center gap-2 text-sm text-slate-500">
                <input type="checkbox" @change="selected = $event.target.checked ? [...allIds] : []" :checked="selected.length && selected.length === allIds.length" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                Select all
            </label>
        </div>
    </div>

    {{-- Bulk action bar --}}
    <div x-show="selected.length" x-cloak class="mb-4 flex flex-wrap items-center gap-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3">
        <span class="text-sm font-semibold text-brand-700"><span x-text="selected.length"></span> selected</span>
        <form method="POST" action="{{ route('admin.reviews.bulk') }}" class="flex flex-wrap items-center gap-2"
              @submit="if (document.querySelector('#bulk-action').value === 'delete' && !confirm('Delete ' + selected.length + ' review(s)? This cannot be undone.')) $event.preventDefault();">
            @csrf
            <input type="hidden" name="ids" :value="selected.join(',')">
            <select id="bulk-action" name="action" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm">
                <option value="approve">Approve</option>
                <option value="unapprove">Unapprove</option>
                <option value="verify">Mark verified purchaser</option>
                <option value="unverify">Remove verified badge</option>
                <option value="testimonial">Mark as testimonial</option>
                <option value="untestimonial">Remove testimonial</option>
                <option value="delete">Delete</option>
            </select>
            <button type="submit" class="btn-primary btn-sm">Apply</button>
        </form>
        <button type="button" @click="selected = []" class="text-sm font-semibold text-slate-500 hover:text-ink-900">Clear</button>
    </div>

    <div class="space-y-4">
        @forelse ($reviews as $review)
            @php $genuine = $review->product && ! $review->product->is_free && $review->user && $review->user->hasPurchased($review->product_id); @endphp
            <div class="card p-5 {{ $genuine ? 'border-l-4 border-l-emerald-400' : '' }}" x-data="{ reply: false }" :class="selected.includes({{ $review->id }}) && 'ring-2 ring-brand-500/40'">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex min-w-0 gap-3">
                        <input type="checkbox" value="{{ $review->id }}" x-model.number="selected" class="mt-1 h-4 w-4 flex-none rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-star-rating :rating="$review->rating" size="h-4 w-4" />
                            <span class="font-semibold text-ink-900">{{ $review->user->name ?? 'User' }}</span>
                            @if ($review->is_approved)
                                <span class="chip bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">Approved</span>
                            @else
                                <span class="chip bg-amber-50 text-amber-700 ring-1 ring-amber-200">Pending</span>
                            @endif
                            @if ($review->is_testimonial)
                                <span class="chip bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200">Testimonial</span>
                            @endif
                            @if ($review->is_verified)
                                <span class="chip bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">✓ Verified purchaser</span>
                            @endif
                            @if ($genuine)
                                <span class="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 px-2 py-0.5 text-[11px] font-bold text-white shadow-sm" title="This reviewer genuinely purchased this paid product">
                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                                    Genuine paid buyer
                                </span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-slate-500">
                            on
                            @if ($review->product)
                                <a href="{{ route('products.show', $review->product) }}" target="_blank" class="font-medium text-brand-600 hover:underline">{{ $review->product->title }}</a>
                            @else <span class="text-slate-400">deleted product</span> @endif
                            · {{ $review->created_at->diffForHumans() }}
                        </p>
                        @if ($review->comment)
                            <p class="mt-2 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ $review->comment }}</p>
                        @endif
                        @if ($review->admin_reply)
                            <div class="mt-2 rounded-lg border-l-4 border-brand-300 bg-brand-50/60 px-3 py-2 text-sm">
                                <p class="text-xs font-semibold text-brand-700">Store reply @if ($review->replied_at)· {{ $review->replied_at->diffForHumans() }}@endif</p>
                                <p class="mt-0.5 text-slate-600">{{ $review->admin_reply }}</p>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                    @if (! $review->is_approved)
                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">@csrf @method('PATCH')<button class="btn-primary btn-sm">Approve</button></form>
                    @else
                        <form method="POST" action="{{ route('admin.reviews.unapprove', $review) }}">@csrf @method('PATCH')<button class="btn-ghost btn-sm">Unapprove</button></form>
                    @endif
                    <form method="POST" action="{{ route('admin.reviews.testimonial', $review) }}">
                        @csrf @method('PATCH')
                        <button class="btn-ghost btn-sm">{{ $review->is_testimonial ? 'Remove from testimonials' : 'Mark as testimonial' }}</button>
                    </form>
                    <form method="POST" action="{{ route('admin.reviews.verified', $review) }}">
                        @csrf @method('PATCH')
                        <button class="btn-ghost btn-sm {{ $review->is_verified ? 'border border-emerald-200 text-emerald-700 hover:bg-emerald-50' : '' }}">{{ $review->is_verified ? 'Remove verified badge' : 'Mark verified purchaser' }}</button>
                    </form>
                    <button type="button" @click="reply = !reply" class="btn-ghost btn-sm border border-brand-200 text-brand-600 hover:bg-brand-50">{{ $review->admin_reply ? 'Edit reply' : 'Reply' }}</button>
                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?');" class="ml-auto">
                        @csrf @method('DELETE')
                        <button class="text-sm font-semibold text-rose-600 hover:text-rose-700">Delete</button>
                    </form>
                </div>

                {{-- Admin reply editor (optional) --}}
                <div x-show="reply" x-cloak x-transition class="mt-4 border-t border-slate-100 pt-4">
                    <form method="POST" action="{{ route('admin.reviews.reply', $review) }}">
                        @csrf @method('PATCH')
                        <label class="text-sm font-semibold text-ink-900">Public reply to this review</label>
                        <textarea name="admin_reply" rows="2" placeholder="Thanks for your feedback! ..."
                                  class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10">{{ $review->admin_reply }}</textarea>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="submit" class="btn-primary btn-sm">Save reply</button>
                            <button type="button" @click="reply = false" class="btn-ghost btn-sm">Cancel</button>
                            <span class="text-xs text-slate-400">Shows publicly under the review. Leave empty and save to remove it.</span>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <x-empty-state title="No reviews" message="Customer reviews will appear here for moderation."
                icon="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
        @endforelse
    </div>

    <div class="mt-6">{{ $reviews->links() }}</div>
    </div>
@endsection
