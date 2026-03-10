<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Technical Support', 'description' => 'Help with technical issues and troubleshooting'],
            ['name' => 'Billing', 'description' => 'Payment, invoices, and subscription inquiries'],
            ['name' => 'Sales', 'description' => 'Pre-sales questions and product information'],
            ['name' => 'General Inquiry', 'description' => 'General questions and feedback'],
        ];

        foreach ($departments as $department) {
            Department::query()->firstOrCreate(['name' => $department['name']], $department);
        }
    }
}
