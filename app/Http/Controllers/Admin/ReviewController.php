<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->string('filter')->toString();

        $reviews = Review::query()
            ->with(['user', 'product'])
            ->when($filter === 'pending', fn ($q) => $q->where('is_approved', false))
            ->when($filter === 'approved', fn ($q) => $q->where('is_approved', true))
            ->when($filter === 'testimonials', fn ($q) => $q->where('is_testimonial', true))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'filter' => $filter,
            'pendingCount' => Review::where('is_approved', false)->count(),
        ]);
    }

    /**
     * Approve (publish) a review.
     */
    public function approve(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => true]);

        if ($review->user) {
            try {
                \Illuminate\Support\Facades\Mail::to($review->user->email)
                    ->send(new \App\Mail\ReviewApprovedMail($review->load(['user', 'product'])));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Review approved and published.');
    }

    /**
     * Unapprove (hide) a review.
     */
    public function unapprove(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => false, 'is_testimonial' => false]);

        return back()->with('success', 'Review hidden.');
    }

    /**
     * Toggle whether a review is featured as a testimonial on the homepage.
     */
    public function toggleTestimonial(Review $review): RedirectResponse
    {
        // Only approved reviews can be testimonials.
        if (! $review->is_approved) {
            $review->is_approved = true;
        }

        $review->is_testimonial = ! $review->is_testimonial;
        $review->save();

        return back()->with('success', $review->is_testimonial
            ? 'Review is now shown as a testimonial.'
            : 'Review removed from testimonials.');
    }

    /**
     * Save (or update/remove) the admin's public reply to a review.
     */
    public function reply(Request $request, Review $review): RedirectResponse
    {
        $data = $request->validate([
            'admin_reply' => ['nullable', 'string', 'max:1000'],
        ]);

        $reply = trim((string) ($data['admin_reply'] ?? ''));

        $review->update([
            'admin_reply' => $reply !== '' ? $reply : null,
            'replied_at' => $reply !== '' ? now() : null,
        ]);

        return back()->with('success', $reply !== '' ? 'Reply saved.' : 'Reply removed.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
