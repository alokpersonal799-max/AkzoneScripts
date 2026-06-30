<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Securely deliver a purchased product file.
     *
     * Access is granted only when the order item belongs to the authenticated
     * user and the parent order has been completed. Files live on the private
     * "products" disk and are never publicly reachable.
     */
    public function download(Request $request, OrderItem $orderItem): StreamedResponse
    {
        $orderItem->load(['order', 'product']);

        // The item must belong to the current user.
        abort_unless($orderItem->order->user_id === $request->user()->id, 403);

        // The order must be paid/completed.
        abort_unless($orderItem->order->isCompleted(), 403, 'This order has not been completed.');

        $product = $orderItem->product;

        abort_if(! $product || ! $product->file_path, 404, 'The download for this product is not available.');

        $disk = Storage::disk('products');

        abort_unless($disk->exists($product->file_path), 404, 'The product file could not be found.');

        // Track how many times the buyer has downloaded the item.
        $orderItem->increment('download_count');

        $downloadName = $product->file_name ?: basename($product->file_path);

        return $disk->download($product->file_path, $downloadName);
    }
}
