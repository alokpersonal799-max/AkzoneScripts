@extends('layouts.admin')

@section('page-title', 'Reviews')

@section('admin')
    <div class="mb-6 flex flex-wrap items-center gap-2">
        @foreach (['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'testimonials' => 'Testimonials'] as $key => $label)
            <a href="{{ route('admin.reviews.index', $key ? ['filter' => $key] : []) }}"
               class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ $filter === $key ? 'bg-brand-600 text-white' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}">
                {{ $label }}@if ($key === 'pending' && $pendingCount) <span class="ml-1 rounded-full bg-amber-400 px-1.5 text-xs text-white">{{ $pendingCount }}</span>@endif
            </a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse ($reviews as $review)
            <div class="card p-5" x-data="{ reply: false }">
                <div class="flex flex-wrap items-start justify-between gap-3">
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
@endsection
