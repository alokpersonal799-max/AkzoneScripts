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
            'stats' => [
                'total' => Review::count(),
                'pending' => Review::where('is_approved', false)->count(),
                'approved' => Review::where('is_approved', true)->count(),
                'verified' => Review::where('is_verified', true)->count(),
                'testimonials' => Review::where('is_testimonial', true)->count(),
            ],
        ]);
    }

    /**
     * Apply an action to many reviews at once.
     */
    public function bulk(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:approve,unapprove,verify,unverify,testimonial,untestimonial,delete'],
            'ids' => ['required', 'string'],
        ]);

        $ids = array_filter(array_map('intval', explode(',', $data['ids'])));

        if (empty($ids)) {
            return back()->with('error', 'No reviews selected.');
        }

        // Operate per-model so the product rating/count is recalculated correctly.
        $reviews = Review::whereIn('id', $ids)->get();

        foreach ($reviews as $review) {
            match ($data['action']) {
                'approve' => $review->update(['is_approved' => true]),
                'unapprove' => $review->update(['is_approved' => false, 'is_testimonial' => false]),
                'verify' => $review->update(['is_verified' => true]),
                'unverify' => $review->update(['is_verified' => false]),
                'testimonial' => $review->update(['is_approved' => true, 'is_testimonial' => true]),
                'untestimonial' => $review->update(['is_testimonial' => false]),
                'delete' => $review->delete(),
            };
        }

        return back()->with('success', 'Bulk action applied to '.$reviews->count().' review(s).');
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
     * Toggle the "Verified purchaser" badge for a review. Admin-controlled, so it
     * can be shown for genuine buyers (any product) or hidden as needed.
     */
    public function toggleVerified(Review $review): RedirectResponse
    {
        $review->is_verified = ! $review->is_verified;
        $review->save();

        return back()->with('success', $review->is_verified
            ? 'Marked as a verified purchaser.'
            : 'Removed the verified purchaser badge.');
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
