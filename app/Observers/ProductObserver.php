<?php

namespace App\Observers;

use App\Mail\NewProductMail;
use App\Mail\PriceDropMail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProductObserver
{
    /**
     * Ensure every product has a unique slug derived from its title before saving.
     */
    public function saving(Product $product): void
    {
        if (empty($product->slug) || $product->isDirty('title')) {
            $product->slug = $this->uniqueSlug($product);
        }
    }

    /**
     * After a product is created, announce it if it is published.
     */
    public function created(Product $product): void
    {
        if ($product->status === 'published') {
            $this->notifyNewProduct($product);
        }
    }

    /**
     * After a product is updated, handle "just published" and price-drop alerts.
     */
    public function updated(Product $product): void
    {
        // Newly published.
        if ($product->wasChanged('status') && $product->status === 'published') {
            $this->notifyNewProduct($product);
        }

        // Price drop on a published product → alert wishlist owners.
        if ($product->status === 'published' && ($product->wasChanged('price') || $product->wasChanged('sale_price'))) {
            $oldCurrent = (float) ($product->getOriginal('sale_price') ?? $product->getOriginal('price'));
            $newCurrent = (float) $product->current_price;

            if ($newCurrent > 0 && $newCurrent < $oldCurrent) {
                $this->notifyPriceDrop($product);
            }
        }
    }

    /**
     * Email all active customers about a new product.
     */
    protected function notifyNewProduct(Product $product): void
    {
        try {
            User::where('role', 'user')->where('is_banned', false)
                ->select('email')->chunk(100, function ($users) use ($product) {
                    foreach ($users as $user) {
                        Mail::to($user->email)->send(new NewProductMail($product));
                    }
                });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Email everyone who wishlisted this product about the price drop.
     */
    protected function notifyPriceDrop(Product $product): void
    {
        try {
            User::whereHas('wishlists', fn ($q) => $q->where('product_id', $product->id))
                ->where('is_banned', false)
                ->chunk(100, function ($users) use ($product) {
                    foreach ($users as $user) {
                        Mail::to($user->email)->send(new PriceDropMail($product, $user));
                    }
                });
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Build a unique slug for the product, appending a counter on collisions.
     */
    protected function uniqueSlug(Product $product): string
    {
        $base = Str::slug($product->title);

        if ($base === '') {
            $base = 'product';
        }

        $slug = $base;
        $counter = 1;

        while (
            Product::where('slug', $slug)
                ->when($product->exists, fn ($query) => $query->whereKeyNot($product->getKey()))
                ->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
