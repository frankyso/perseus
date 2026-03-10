<?php

namespace App\Filament\Resources\KnowledgebaseCategoryResource\Pages;

use App\Filament\Resources\KnowledgebaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKnowledgebaseCategories extends ListRecords
{
    protected static string $resource = KnowledgebaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
