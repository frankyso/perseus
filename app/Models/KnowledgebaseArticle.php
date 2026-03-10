<?php

namespace App\Models;

use App\Enums\KnowledgebaseArticleStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class KnowledgebaseArticle extends Model
{
    /** @use HasFactory<\Database\Factories\KnowledgebaseArticleFactory> */
    use HasFactory, Searchable;

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

    /**
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'body' => strip_tags($this->body ?? ''),
            'status' => $this->getRawOriginal('status'),
            'knowledgebase_category_id' => (int) $this->knowledgebase_category_id,
            'published_at' => $this->published_at?->timestamp,
            'views_count' => (int) $this->views_count,
            'sort_order' => (int) $this->sort_order,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->getRawOriginal('status') === 'published' && $this->published_at !== null;
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }
}
