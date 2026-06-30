<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramBot extends Model
{
    protected $fillable = ['name', 'token', 'chat_id', 'events', 'is_active'];

    protected function casts(): array
    {
        return [
            'events' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * The notification event types a bot can be subscribed to.
     *
     * @var array<string, string>
     */
    public const EVENTS = [
        'registration' => 'New user registration',
        'product_added' => 'New product added',
        'category_added' => 'New category added',
        'promotion' => 'Hero promotion updated',
        'purchase' => 'Product purchased',
        'review' => 'New product review',
        'free_download' => 'Free product downloaded',
        'custom' => 'Custom broadcast messages',
    ];

    /**
     * Whether this bot should send the given event.
     */
    public function sendsEvent(string $event): bool
    {
        $events = $this->events ?? [];

        return in_array('all', $events, true) || in_array($event, $events, true);
    }

    /**
     * Masked token for display (never expose the full token in the UI).
     */
    public function getMaskedTokenAttribute(): string
    {
        $token = (string) $this->token;

        if (strlen($token) <= 10) {
            return str_repeat('•', strlen($token));
        }

        return substr($token, 0, 6).str_repeat('•', 6).substr($token, -4);
    }
}
