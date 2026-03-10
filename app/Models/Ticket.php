<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory;

    protected $fillable = [
        'reference',
        'subject',
        'description',
        'status',
        'priority',
        'user_id',
        'department_id',
        'category_id',
        'assigned_to',
        'resolved_at',
        'closed_at',
        'sla_policy_id',
        'first_response_at',
        'first_response_due_at',
        'resolution_due_at',
        'first_response_breached',
        'resolution_breached',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'priority' => TicketPriority::class,
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'first_response_at' => 'datetime',
            'first_response_due_at' => 'datetime',
            'resolution_due_at' => 'datetime',
            'first_response_breached' => 'boolean',
            'resolution_breached' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket): void {
            if (empty($ticket->reference)) {
                $ticket->reference = 'TKT-'.strtoupper(uniqid());
            }

            $ticket->applySlaPolicy();
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * @return BelongsTo<TicketCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return BelongsTo<SlaPolicy, $this>
     */
    public function slaPolicy(): BelongsTo
    {
        return $this->belongsTo(SlaPolicy::class);
    }

    /**
     * @return HasMany<TicketReply, $this>
     */
    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    /**
     * @return HasMany<TicketAttachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function isOpen(): bool
    {
        $status = $this->status instanceof TicketStatus ? $this->status : TicketStatus::tryFrom($this->status ?? '');

        return $status?->isOpen() ?? false;
    }

    public function applySlaPolicy(): void
    {
        $priority = $this->priority instanceof TicketPriority ? $this->priority->value : ($this->priority ?? 'medium');
        $sla = SlaPolicy::findByPriority($priority);

        if (! $sla) {
            return;
        }

        $this->sla_policy_id = $sla->id;
        $this->first_response_due_at = now()->addHours($sla->first_response_hours);
        $this->resolution_due_at = now()->addHours($sla->resolution_hours);
    }

    public function recordFirstResponse(): void
    {
        if ($this->first_response_at) {
            return;
        }

        $this->first_response_at = now();
        $this->first_response_breached = $this->first_response_due_at
            && now()->isAfter($this->first_response_due_at);
        $this->save();
    }

    public function checkResolutionBreach(): void
    {
        if (! $this->resolution_due_at || $this->resolution_breached) {
            return;
        }

        if (now()->isAfter($this->resolution_due_at) && $this->isOpen()) {
            $this->resolution_breached = true;
            $this->save();
        }
    }

    public function getFirstResponseTimeAttribute(): ?string
    {
        if (! $this->first_response_at || ! $this->created_at) {
            return null;
        }

        $diff = $this->created_at->diff($this->first_response_at);

        if ($diff->days > 0) {
            return $diff->days.'d '.$diff->h.'h';
        }

        if ($diff->h > 0) {
            return $diff->h.'h '.$diff->i.'m';
        }

        return $diff->i.'m';
    }

    public function getResolutionTimeAttribute(): ?string
    {
        if (! $this->resolved_at || ! $this->created_at) {
            return null;
        }

        $diff = $this->created_at->diff($this->resolved_at);

        if ($diff->days > 0) {
            return $diff->days.'d '.$diff->h.'h';
        }

        if ($diff->h > 0) {
            return $diff->h.'h '.$diff->i.'m';
        }

        return $diff->i.'m';
    }

    public function getSlaStatusAttribute(): string
    {
        if (! $this->sla_policy_id) {
            return 'no_sla';
        }

        if ($this->resolution_breached || $this->first_response_breached) {
            return 'breached';
        }

        if ($this->isOpen()) {
            if ($this->resolution_due_at && now()->isAfter($this->resolution_due_at)) {
                return 'breached';
            }

            if ($this->first_response_due_at && ! $this->first_response_at && now()->isAfter($this->first_response_due_at)) {
                return 'breached';
            }

            if ($this->resolution_due_at && now()->diffInHours($this->resolution_due_at) <= 2) {
                return 'at_risk';
            }
        }

        return 'on_track';
    }
}
