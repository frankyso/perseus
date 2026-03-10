<?php

use App\Models\KnowledgebaseArticle;
use App\Models\KnowledgebaseCategory;

test('anyone can access knowledgebase index', function () {
    $response = $this->get(route('knowledgebase.index'));

    $response->assertOk();
});

test('knowledgebase index shows active categories with published article counts', function () {
    $activeCategory = KnowledgebaseCategory::factory()->create(['is_active' => true]);
    KnowledgebaseCategory::factory()->create(['is_active' => false]);

    KnowledgebaseArticle::factory()->published()->count(3)->create([
        'knowledgebase_category_id' => $activeCategory->id,
    ]);

    $response = $this->get(route('knowledgebase.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('knowledgebase/index')
        ->has('categories', 1)
        ->where('categories.0.published_articles_count', 3)
    );
});

test('anyone can view a category with published articles', function () {
    $category = KnowledgebaseCategory::factory()->create(['is_active' => true]);
    KnowledgebaseArticle::factory()->published()->count(2)->create([
        'knowledgebase_category_id' => $category->id,
    ]);

    $response = $this->get(route('knowledgebase.category', $category->slug));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('knowledgebase/category')
        ->has('articles.data', 2)
    );
});

test('category page does not show draft articles', function () {
    $category = KnowledgebaseCategory::factory()->create(['is_active' => true]);

    KnowledgebaseArticle::factory()->published()->create([
        'knowledgebase_category_id' => $category->id,
    ]);
    KnowledgebaseArticle::factory()->create([
        'knowledgebase_category_id' => $category->id,
        'status' => 'draft',
        'published_at' => null,
    ]);

    $response = $this->get(route('knowledgebase.category', $category->slug));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('knowledgebase/category')
        ->has('articles.data', 1)
    );
});

test('anyone can view a published article', function () {
    $category = KnowledgebaseCategory::factory()->create(['is_active' => true]);
    $article = KnowledgebaseArticle::factory()->published()->create([
        'knowledgebase_category_id' => $category->id,
    ]);

    $response = $this->get(route('knowledgebase.show', [$category->slug, $article->slug]));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('knowledgebase/show')
        ->where('article.id', $article->id)
    );
});

test('viewing an article increments view count', function () {
    $category = KnowledgebaseCategory::factory()->create(['is_active' => true]);
    $article = KnowledgebaseArticle::factory()->published()->create([
        'knowledgebase_category_id' => $category->id,
        'views_count' => 5,
    ]);

    $this->get(route('knowledgebase.show', [$category->slug, $article->slug]));

    expect($article->refresh()->views_count)->toBe(6);
});

test('search returns matching published articles', function () {
    $category = KnowledgebaseCategory::factory()->create(['is_active' => true]);

    KnowledgebaseArticle::factory()->published()->create([
        'knowledgebase_category_id' => $category->id,
        'title' => 'How to reset your password',
        'body' => 'Steps to reset your password.',
    ]);

    KnowledgebaseArticle::factory()->published()->create([
        'knowledgebase_category_id' => $category->id,
        'title' => 'Billing FAQ',
        'body' => 'Common billing questions.',
    ]);

    $response = $this->get(route('knowledgebase.search', ['query' => 'password']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('knowledgebase/search')
        ->has('articles.data', 1)
        ->where('articles.data.0.title', 'How to reset your password')
    );
});

test('search does not return draft articles', function () {
    $category = KnowledgebaseCategory::factory()->create(['is_active' => true]);

    KnowledgebaseArticle::factory()->create([
        'knowledgebase_category_id' => $category->id,
        'title' => 'Draft article about secrets',
        'body' => 'This should not appear in search.',
        'status' => 'draft',
        'published_at' => null,
    ]);

    KnowledgebaseArticle::factory()->published()->create([
        'knowledgebase_category_id' => $category->id,
        'title' => 'Published article about secrets',
        'body' => 'This should appear in search.',
    ]);

    $response = $this->get(route('knowledgebase.search', ['query' => 'secrets']));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('knowledgebase/search')
        ->has('articles.data', 1)
        ->where('articles.data.0.title', 'Published article about secrets')
    );
});
