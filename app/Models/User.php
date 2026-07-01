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
        'phone',
        'phone_country',
        'last_login_at',
        'last_login_ip',
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
            'last_login_at' => 'datetime',
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
     * The user's avatar URL. Falls back to an auto-generated initials avatar
     * (inline SVG) so every user always has a nice profile picture — even
     * with no uploaded image and no internet connection.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar);
        }

        $initial = strtoupper(mb_substr(trim((string) $this->name) ?: 'U', 0, 1));
        $palette = ['#2563eb', '#7c3aed', '#db2777', '#059669', '#ea580c', '#0891b2', '#d946ef', '#f59e0b'];
        $bg = $palette[abs(crc32((string) ($this->email ?: $this->name ?: 'u'))) % count($palette)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="128" height="128" viewBox="0 0 128 128">'
            .'<rect width="128" height="128" rx="28" fill="'.$bg.'"/>'
            .'<text x="64" y="64" dy=".12em" font-family="Arial,Helvetica,sans-serif" font-size="60" font-weight="700" fill="#ffffff" text-anchor="middle" dominant-baseline="middle">'.htmlspecialchars($initial, ENT_QUOTES).'</text>'
            .'</svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * The full phone number including country dial code, if set.
     */
    public function getFullPhoneAttribute(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        return trim(($this->phone_country ? $this->phone_country.' ' : '').$this->phone);
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
     * Announcement inbox rows delivered to this user.
     *
     * @return HasMany<AnnouncementRecipient>
     */
    public function announcementRecipients(): HasMany
    {
        return $this->hasMany(AnnouncementRecipient::class);
    }

    /**
     * Count of unread announcements in the user's inbox.
     */
    public function unreadAnnouncementsCount(): int
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('announcement_recipients')) {
            return 0;
        }

        return $this->announcementRecipients()->whereNull('read_at')->count();
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
