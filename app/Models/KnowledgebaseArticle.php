<?php

namespace App\Models;

use App\Enums\KnowledgebaseArticleStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgebaseArticle extends Model
{
    /** @use HasFactory<\Database\Factories\KnowledgebaseArticleFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'knowledgebase_category_id',
        'author_id',
        'status',
        'sort_order',
        'views_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => KnowledgebaseArticleStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<KnowledgebaseCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgebaseCategory::class, 'knowledgebase_category_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @param  Builder<KnowledgebaseArticle>  $query
     * @return Builder<KnowledgebaseArticle>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', KnowledgebaseArticleStatus::Published)->whereNotNull('published_at');
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}
