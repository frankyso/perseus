<?php

namespace App\Filament\Resources\KnowledgebaseCategoryResource\Pages;

use App\Filament\Resources\KnowledgebaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKnowledgebaseCategory extends EditRecord
{
    protected static string $resource = KnowledgebaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
