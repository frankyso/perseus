<?php

namespace Database\Seeders;

use App\Models\KnowledgebaseCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KnowledgebaseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Getting Started', 'description' => 'Learn the basics and get up and running quickly', 'sort_order' => 1],
            ['name' => 'Account & Billing', 'description' => 'Manage your account, subscriptions, and payments', 'sort_order' => 2],
            ['name' => 'Troubleshooting', 'description' => 'Solutions to common problems and issues', 'sort_order' => 3],
            ['name' => 'FAQs', 'description' => 'Frequently asked questions', 'sort_order' => 4],
        ];

        foreach ($categories as $category) {
            KnowledgebaseCategory::query()->firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                array_merge($category, ['slug' => Str::slug($category['name'])])
            );
        }
    }
}
