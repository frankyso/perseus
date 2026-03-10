<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class TicketCategorySeeder extends Seeder
{
    public function run(): void
    {
        $techSupport = Department::query()->where('name', 'Technical Support')->first();
        $billing = Department::query()->where('name', 'Billing')->first();

        $categories = [
            ['name' => 'Bug Report', 'department_id' => $techSupport?->id],
            ['name' => 'Feature Request', 'department_id' => $techSupport?->id],
            ['name' => 'Installation Help', 'department_id' => $techSupport?->id],
            ['name' => 'Payment Issue', 'department_id' => $billing?->id],
            ['name' => 'Refund Request', 'department_id' => $billing?->id],
            ['name' => 'General Question', 'department_id' => null],
        ];

        foreach ($categories as $category) {
            TicketCategory::query()->firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
