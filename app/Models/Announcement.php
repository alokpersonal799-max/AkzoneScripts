<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    protected $fillable = [
        'title', 'body', 'theme', 'audience', 'status', 'scheduled_at', 'sent_at',
        'allow_reply', 'reply_types', 'product_id', 'action_url', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'allow_reply' => 'boolean',
            'reply_types' => 'array',
        ];
    }

    /**
     * Prebuilt announcement themes with colours and icons.
     *
     * @return array<string, array<string, string>>
     */
    public static function themes(): array
    {
        return [
            'custom' => ['label' => 'Custom', 'color' => 'slate', 'icon' => 'M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z'],
            'offer' => ['label' => 'New Offer', 'color' => 'emerald', 'icon' => 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z'],
            'coming_soon' => ['label' => 'Coming Soon', 'color' => 'indigo', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
            'new_product' => ['label' => 'New Product', 'color' => 'brand', 'icon' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z'],
            'maintenance' => ['label' => 'Maintenance', 'color' => 'amber', 'icon' => 'M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63'],
            'warning' => ['label' => 'Warning', 'color' => 'rose', 'icon' => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z'],
        ];
    }

    public function themeMeta(): array
    {
        return static::themes()[$this->theme] ?? static::themes()['custom'];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(AnnouncementRecipient::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(AnnouncementReply::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Which reply formats the admin allows for this announcement.
     *
     * @return array<int, string>
     */
    public function allowedReplyTypes(): array
    {
        return $this->allow_reply ? (array) ($this->reply_types ?? []) : [];
    }

    public function allowsReplyType(string $type): bool
    {
        return in_array($type, $this->allowedReplyTypes(), true);
    }

    /**
     * Deliver the announcement: create recipient rows and mark it sent.
     */
    public function send(): void
    {
        $userIds = $this->audience === 'all'
            ? User::query()->pluck('id')
            : $this->recipients()->pluck('user_id');

        // For "all" we (re)create recipient rows for every user.
        if ($this->audience === 'all') {
            $now = now();
            $rows = $userIds->map(fn ($id) => [
                'announcement_id' => $this->id,
                'user_id' => $id,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            foreach (array_chunk($rows, 500) as $chunk) {
                AnnouncementRecipient::insertOrIgnore($chunk);
            }
        }

        $this->update(['status' => 'sent', 'sent_at' => now()]);

        AdminNotification::notifyAdmins(
            'announcement',
            'Announcement sent: '.$this->title,
            'Delivered to '.$this->recipients()->count().' user(s).',
            route('admin.announcements.show', $this)
        );
    }
}
