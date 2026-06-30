<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    /**
     * Session key under which the cart product IDs are stored.
     */
    public const SESSION_KEY = 'cart';

    /**
     * Get the raw list of product IDs currently in the cart.
     *
     * @return array<int, int>
     */
    public function ids(): array
    {
        return array_values(array_unique(array_map('intval', session(self::SESSION_KEY, []))));
    }

    /**
     * Add a product to the cart. Digital goods are unique, so quantity is
     * always one and adding the same product twice is a no-op.
     */
    public function add(Product $product): void
    {
        $ids = $this->ids();

        if (! in_array($product->id, $ids, true)) {
            $ids[] = $product->id;
        }

        session([self::SESSION_KEY => $ids]);
    }

    /**
     * Remove a product from the cart.
     */
    public function remove(int $productId): void
    {
        $ids = array_values(array_filter(
            $this->ids(),
            fn (int $id): bool => $id !== $productId
        ));

        session([self::SESSION_KEY => $ids]);
    }

    /**
     * Empty the cart entirely.
     */
    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * Determine whether a product is already in the cart.
     */
    public function has(int $productId): bool
    {
        return in_array($productId, $this->ids(), true);
    }

    /**
     * Resolve the cart product IDs into published Product models.
     *
     * @return Collection<int, Product>
     */
    public function items(): Collection
    {
        $ids = $this->ids();

        if (empty($ids)) {
            return collect();
        }

        return Product::query()
            ->published()
            ->with('category')
            ->whereIn('id', $ids)
            ->get();
    }

    /**
     * Total number of items in the cart.
     */
    public function count(): int
    {
        return count($this->ids());
    }

    /**
     * Subtotal of all cart items using each product's effective price.
     */
    public function subtotal(): float
    {
        return (float) $this->items()->sum(fn (Product $product): float => $product->current_price);
    }
}
