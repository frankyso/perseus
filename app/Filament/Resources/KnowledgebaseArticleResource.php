<?php

namespace App\Filament\Resources;

use App\Enums\KnowledgebaseArticleStatus;
use App\Filament\Resources\KnowledgebaseArticleResource\Pages;
use App\Models\KnowledgebaseArticle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class KnowledgebaseArticleResource extends Resource
{
    protected static ?string $model = KnowledgebaseArticle::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Knowledgebase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('excerpt')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('body')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('knowledgebase_category_id')
                    ->relationship('category', 'name')
                    ->preload()
                    ->searchable()
                    ->label('Category'),
                Forms\Components\Select::make('status')
                    ->options(KnowledgebaseArticleStatus::class)
                    ->default(KnowledgebaseArticleStatus::Draft)
                    ->required(),
                Forms\Components\DateTimePicker::make('published_at'),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(KnowledgebaseArticleStatus::class),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKnowledgebaseArticles::route('/'),
            'create' => Pages\CreateKnowledgebaseArticle::route('/create'),
            'edit' => Pages\EditKnowledgebaseArticle::route('/{record}/edit'),
        ];
    }
}
