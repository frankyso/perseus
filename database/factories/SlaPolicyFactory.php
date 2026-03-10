<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SlaPolicy>
 */
class SlaPolicyFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true).' SLA',
            'priority' => fake()->randomElement(TicketPriority::cases())->value,
            'first_response_hours' => fake()->randomElement([1, 4, 8, 24]),
            'resolution_hours' => fake()->randomElement([4, 24, 48, 72]),
            'is_active' => true,
        ];
    }
}
