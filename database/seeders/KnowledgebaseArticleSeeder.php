<?php

namespace Database\Seeders;

use App\Models\KnowledgebaseArticle;
use App\Models\KnowledgebaseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class KnowledgebaseArticleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->first();

        if (! $admin) {
            return;
        }

        $categories = KnowledgebaseCategory::all();

        foreach ($categories as $category) {
            KnowledgebaseArticle::factory()
                ->count(3)
                ->published()
                ->create([
                    'knowledgebase_category_id' => $category->id,
                    'author_id' => $admin->id,
                ]);
        }
    }
}
