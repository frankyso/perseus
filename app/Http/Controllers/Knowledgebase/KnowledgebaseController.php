<?php

namespace App\Http\Controllers\Knowledgebase;

use App\Enums\KnowledgebaseArticleStatus;
use App\Http\Controllers\Controller;
use App\Models\KnowledgebaseArticle;
use App\Models\KnowledgebaseCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KnowledgebaseController extends Controller
{
    /**
     * Display the knowledgebase index with active categories.
     */
    public function index(): Response
    {
        $categories = KnowledgebaseCategory::query()
            ->where('is_active', true)
            ->withCount('publishedArticles')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('knowledgebase/index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Display a category with its published articles.
     */
    public function category(KnowledgebaseCategory $category): Response
    {
        abort_unless($category->is_active, 404);

        $articles = $category->publishedArticles()
            ->orderBy('sort_order')
            ->paginate(15);

        return Inertia::render('knowledgebase/category', [
            'category' => $category,
            'articles' => $articles,
        ]);
    }

    /**
     * Display a knowledgebase article.
     */
    public function show(KnowledgebaseCategory $category, KnowledgebaseArticle $article): Response
    {
        abort_unless($category->is_active, 404);
        abort_unless($article->status === KnowledgebaseArticleStatus::Published && $article->published_at !== null, 404);

        $article->incrementViews();

        $article->load('author');

        return Inertia::render('knowledgebase/show', [
            'category' => $category,
            'article' => $article,
        ]);
    }

    /**
     * Search knowledgebase articles.
     */
    public function search(Request $request): Response
    {
        $query = $request->input('query', '');

        $articles = KnowledgebaseArticle::query()
            ->published()
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('body', 'like', "%{$query}%");
                });
            })
            ->with('category')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('knowledgebase/search', [
            'articles' => $articles,
            'query' => $query,
        ]);
    }
}
