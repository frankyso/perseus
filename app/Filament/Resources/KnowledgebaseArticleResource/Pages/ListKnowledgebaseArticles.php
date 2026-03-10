<?php

namespace App\Filament\Resources\KnowledgebaseArticleResource\Pages;

use App\Filament\Resources\KnowledgebaseArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKnowledgebaseArticles extends ListRecords
{
    protected static string $resource = KnowledgebaseArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
