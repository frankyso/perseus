<?php

namespace App\Filament\Resources;

use App\Enums\TicketPriority;
use App\Filament\Resources\SlaPolicyResource\Pages;
use App\Models\SlaPolicy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SlaPolicyResource extends Resource
{
    protected static ?string $model = SlaPolicy::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('priority')
                    ->options(TicketPriority::class)
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('first_response_hours')
                    ->label('First Response Time (hours)')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->suffix('hours'),
                Forms\Components\TextInput::make('resolution_hours')
                    ->label('Resolution Time (hours)')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->suffix('hours'),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TicketPriority::tryFrom($state)?->getLabel() ?? $state)
                    ->color(fn (string $state): string => TicketPriority::tryFrom($state)?->getColor() ?? 'gray'),
                Tables\Columns\TextColumn::make('first_response_hours')
                    ->label('First Response')
                    ->suffix(' hours'),
                Tables\Columns\TextColumn::make('resolution_hours')
                    ->label('Resolution')
                    ->suffix(' hours'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSlaPolicies::route('/'),
            'create' => Pages\CreateSlaPolicy::route('/create'),
            'edit' => Pages\EditSlaPolicy::route('/{record}/edit'),
        ];
    }
}
