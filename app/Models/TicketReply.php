<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketReply extends Model
{
    /** @use HasFactory<\Database\Factories\TicketReplyFactory> */
    use HasFactory;

    protected $fillable = [
        'body',
        'ticket_id',
        'user_id',
        'is_internal_note',
    ];

    protected function casts(): array
    {
        return [
            'is_internal_note' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (TicketReply $reply): void {
            if ($reply->is_internal_note) {
                return;
            }

            $ticket = $reply->ticket;
            $user = $reply->user;

            if ($user && $user->id !== $ticket->user_id) {
                $ticket->recordFirstResponse();
            }
        });
    }

    /**
     * @return BelongsTo<Ticket, $this>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<TicketAttachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }
}
