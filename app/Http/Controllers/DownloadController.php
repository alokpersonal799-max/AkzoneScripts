<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Free products: record a free order (so it's tracked & reviewable),
     * count a download, and stream the file directly — no checkout needed.
     */
    public function free(Request $request, Product $product): RedirectResponse|StreamedResponse
    {
        abort_unless($product->status === 'published', 404);
        abort_unless($product->current_price <= 0, 404);

        $user = $request->user();
        $disk = Storage::disk('products');

        $isExternal = $product->is_external_file;

        if (! $isExternal && (! $product->file_path || ! $disk->exists($product->file_path))) {
            return back()->with('error', 'This free download is not available yet. Please check back soon.');
        }

        // Record a completed free order once, so it shows in My Purchases and can be reviewed.
        if (! $user->hasPurchased($product->id)) {
            $order = Order::create([
                'user_id' => $user->id,
                'subtotal' => 0,
                'tax' => 0,
                'discount' => 0,
                'total' => 0,
                'status' => 'completed',
                'payment_method' => 'free',
                'transaction_id' => strtoupper('FREE-'.uniqid()),
                'billing_name' => $user->name,
                'billing_email' => $user->email,
                'paid_at' => now(),
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'product_title' => $product->title,
                'price' => 0,
            ]);
        }

        // Free products track downloads (not sales).
        $product->incrementQuietly('downloads');

        if ($isExternal) {
            return redirect()->away($product->external_url);
        }

        return $disk->download($product->file_path, $product->file_name ?: basename($product->file_path));
    }

    /**
     * Securely deliver a purchased product file.
     *
     * Access is granted only when the order item belongs to the authenticated
     * user and the parent order has been completed. Files live on the private
     * "products" disk and are never publicly reachable.
     */
    public function download(Request $request, OrderItem $orderItem): StreamedResponse|RedirectResponse
    {
        $orderItem->load(['order', 'product']);

        // The item must belong to the current user.
        abort_unless($orderItem->order->user_id === $request->user()->id, 403);

        // The order must be paid/completed.
        abort_unless($orderItem->order->isCompleted(), 403, 'This order has not been completed.');

        // Links generated with an expiry carry a signature that must still be valid.
        if ($request->has('signature') && ! $request->hasValidSignature()) {
            return redirect()->route('dashboard.purchases')
                ->with('error', 'This download link has expired. Open My Purchases to generate a fresh one.');
        }

        $product = $orderItem->product;

        abort_if(! $product || ! $product->has_downloadable, 404, 'The download for this product is not available.');

        // Enforce the per-buyer download limit (0 / null = unlimited).
        $limit = (int) ($product->download_limit ?? 0);
        if ($limit > 0 && $orderItem->download_count >= $limit) {
            return redirect()->route('dashboard.purchases')
                ->with('error', 'You have reached the download limit for "'.$product->title.'". Contact support if you need access again.');
        }

        // External products: count the access and redirect to the hosted link.
        if ($product->is_external_file) {
            $orderItem->increment('download_count');

            return redirect()->away($product->external_url);
        }

        $disk = Storage::disk('products');

        abort_unless($disk->exists($product->file_path), 404, 'The product file could not be found.');

        // Track how many times the buyer has downloaded the item.
        $orderItem->increment('download_count');

        $downloadName = $product->file_name ?: basename($product->file_path);

        return $disk->download($product->file_path, $downloadName);
    }
}
