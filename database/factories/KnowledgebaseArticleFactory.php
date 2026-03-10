<?php

namespace Database\Factories;

use App\Models\KnowledgebaseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KnowledgebaseArticle>
 */
class KnowledgebaseArticleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->randomNumber(5),
            'excerpt' => fake()->paragraph(),
            'body' => fake()->paragraphs(5, true),
            'knowledgebase_category_id' => KnowledgebaseCategory::factory(),
            'author_id' => User::factory(),
            'status' => 'draft',
            'sort_order' => 0,
            'views_count' => 0,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
