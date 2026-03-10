<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SlaPolicy extends Model
{
    /** @use HasFactory<\Database\Factories\SlaPolicyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'priority',
        'first_response_hours',
        'resolution_hours',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'first_response_hours' => 'integer',
            'resolution_hours' => 'integer',
        ];
    }

    /**
     * @return HasMany<Ticket, $this>
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public static function findByPriority(string $priority): ?self
    {
        return static::query()
            ->where('priority', $priority)
            ->where('is_active', true)
            ->first();
    }
}
