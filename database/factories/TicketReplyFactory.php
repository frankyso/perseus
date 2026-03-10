<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketReply>
 */
class TicketReplyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'body' => fake()->paragraphs(2, true),
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'is_internal_note' => false,
        ];
    }

    public function internalNote(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_internal_note' => true,
        ]);
    }
}
