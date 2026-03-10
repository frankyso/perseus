<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Department;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
            'status' => TicketStatus::Open,
            'priority' => fake()->randomElement(TicketPriority::cases()),
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'category_id' => TicketCategory::factory(),
        ];
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Resolved,
            'resolved_at' => now(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Closed,
            'resolved_at' => now()->subDay(),
            'closed_at' => now(),
        ]);
    }

    public function assignedTo(User $agent): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_to' => $agent->id,
            'status' => TicketStatus::InProgress,
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TicketPriority::Urgent,
        ]);
    }
}
