<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a review for a product the user has purchased.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $user = $request->user();
        $hasPurchased = $user->hasPurchased($product->id);

        // Paid products require a verified purchase. Free products may be
        // reviewed by any logged-in user (without a verified badge).
        if (! $product->is_free && ! $hasPurchased) {
            return back()->with('error', 'You can review this product only after purchasing it.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        // updateOrCreate so a user editing their review does not violate the
        // unique [product_id, user_id] constraint. New/edited reviews are
        // unapproved until an admin verifies them.
        $product->reviews()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'is_approved' => false,
                // Verified only when a paid product was actually purchased.
                'is_verified' => ! $product->is_free && $hasPurchased,
            ]
        );

        \App\Models\AdminNotification::notifyAdmins('review', 'New review to approve', $user->name.' on '.$product->title, route('admin.reviews.index', ['filter' => 'pending']));

        app(\App\Services\TelegramService::class)->notify('review', \App\Support\TelegramMessages::review($user, $product, (int) $validated['rating']));

        return back()->with('success', 'Thanks for your review! It will appear once an admin approves it.');
    }
}
