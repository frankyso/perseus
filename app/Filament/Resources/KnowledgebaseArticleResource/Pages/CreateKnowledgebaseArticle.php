<?php

namespace App\Filament\Resources\KnowledgebaseArticleResource\Pages;

use App\Filament\Resources\KnowledgebaseArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKnowledgebaseArticle extends CreateRecord
{
    protected static string $resource = KnowledgebaseArticleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['author_id'] = auth()->id();

        return $data;
    }
}
