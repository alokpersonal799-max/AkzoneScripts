<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessage extends Model
{
    protected $fillable = [
        'ticket_id', 'user_id', 'is_admin', 'message', 'attachment_path', 'attachment_name',
    ];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Ticket, TicketMessage>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * @return BelongsTo<User, TicketMessage>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
