<?php

namespace App\Observers;

use App\Models\Product;
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
