<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgebaseCategory extends Model
{
    /** @use HasFactory<\Database\Factories\KnowledgebaseCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<KnowledgebaseArticle, $this>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(KnowledgebaseArticle::class);
    }

    /**
     * @return HasMany<KnowledgebaseArticle, $this>
     */
    public function publishedArticles(): HasMany
    {
        return $this->hasMany(KnowledgebaseArticle::class)
            ->where('status', 'published')
            ->whereNotNull('published_at');
    }
}
