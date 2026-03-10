<?php

use App\Enums\KnowledgebaseArticleStatus;
use App\Models\KnowledgebaseArticle;
use App\Models\KnowledgebaseCategory;
use App\Models\User;

test('article belongs to category', function () {
    $category = KnowledgebaseCategory::factory()->create();
    $article = KnowledgebaseArticle::factory()->create([
        'knowledgebase_category_id' => $category->id,
    ]);

    expect($article->category->id)->toBe($category->id);
});

test('article belongs to author', function () {
    $author = User::factory()->create();
    $article = KnowledgebaseArticle::factory()->create([
        'author_id' => $author->id,
    ]);

    expect($article->author->id)->toBe($author->id);
});

test('published scope filters correctly', function () {
    KnowledgebaseArticle::factory()->published()->count(2)->create();
    KnowledgebaseArticle::factory()->count(3)->create([
        'status' => KnowledgebaseArticleStatus::Draft,
        'published_at' => null,
    ]);

    $published = KnowledgebaseArticle::query()->published()->get();

    expect($published)->toHaveCount(2);
});

test('incrementViews increments views_count', function () {
    $article = KnowledgebaseArticle::factory()->create(['views_count' => 0]);

    $article->incrementViews();

    expect($article->refresh()->views_count)->toBe(1);

    $article->incrementViews();
    $article->incrementViews();

    expect($article->refresh()->views_count)->toBe(3);
});
