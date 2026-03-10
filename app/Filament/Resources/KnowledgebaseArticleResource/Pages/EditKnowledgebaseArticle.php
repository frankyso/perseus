<?php

namespace App\Filament\Resources\KnowledgebaseArticleResource\Pages;

use App\Filament\Resources\KnowledgebaseArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKnowledgebaseArticle extends EditRecord
{
    protected static string $resource = KnowledgebaseArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
