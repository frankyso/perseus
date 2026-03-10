<?php

namespace Database\Seeders;

use App\Models\SlaPolicy;
use Illuminate\Database\Seeder;

class SlaPolicySeeder extends Seeder
{
    public function run(): void
    {
        $policies = [
            [
                'name' => 'Urgent Priority SLA',
                'priority' => 'urgent',
                'first_response_hours' => 1,
                'resolution_hours' => 4,
            ],
            [
                'name' => 'High Priority SLA',
                'priority' => 'high',
                'first_response_hours' => 4,
                'resolution_hours' => 24,
            ],
            [
                'name' => 'Medium Priority SLA',
                'priority' => 'medium',
                'first_response_hours' => 8,
                'resolution_hours' => 48,
            ],
            [
                'name' => 'Low Priority SLA',
                'priority' => 'low',
                'first_response_hours' => 24,
                'resolution_hours' => 72,
            ],
        ];

        foreach ($policies as $policy) {
            SlaPolicy::query()->updateOrCreate(
                ['priority' => $policy['priority']],
                $policy
            );
        }
    }
}
