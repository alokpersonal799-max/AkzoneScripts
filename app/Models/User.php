<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_banned',
        'avatar',
        'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
        ];
    }

    /**
     * Determine if the user has administrative privileges.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Whether the user's email is verified.
     */
    public function hasVerifiedEmail(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    /**
     * Support tickets opened by the user.
     *
     * @return HasMany<Ticket>
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Orders placed by the user.
     *
     * @return HasMany<Order>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Reviews written by the user.
     *
     * @return HasMany<Review>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Wishlist entries belonging to the user.
     *
     * @return HasMany<Wishlist>
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Products the user has wishlisted.
     *
     * @return BelongsToMany<Product>
     */
    public function wishlistedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withTimestamps();
    }

    /**
     * Query for all distinct products the user has successfully purchased
     * (i.e. that appear in one of their completed orders).
     *
     * Returns a Product query builder, so it supports ->take(), ->get(),
     * ->paginate() and further constraints at the call site.
     *
     * @return \Illuminate\Database\Eloquent\Builder<Product>
     */
    public function purchasedProducts(): \Illuminate\Database\Eloquent\Builder
    {
        return Product::query()
            ->whereIn('products.id', function ($query): void {
                $query->select('order_items.product_id')
                    ->from('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.user_id', $this->id)
                    ->where('orders.status', 'completed')
                    ->whereNotNull('order_items.product_id');
            });
    }

    /**
     * Determine whether the user has purchased the given product.
     */
    public function hasPurchased(int $productId): bool
    {
        return Order::query()
            ->where('user_id', $this->id)
            ->where('status', 'completed')
            ->whereHas('items', fn ($query) => $query->where('product_id', $productId))
            ->exists();
    }
}
