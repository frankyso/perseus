<?php

namespace App\Filament\Resources;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers\TicketRepliesRelationManager;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Support';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->where('status', TicketStatus::Open)
            ->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options(TicketStatus::class)
                    ->default(TicketStatus::Open)
                    ->required(),
                Forms\Components\Select::make('priority')
                    ->options(TicketPriority::class)
                    ->default(TicketPriority::Medium)
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Customer')
                    ->required(),
                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'name')
                    ->preload(),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->preload(),
                Forms\Components\Select::make('assigned_to')
                    ->label('Assigned Agent')
                    ->options(fn (): array => User::query()
                        ->role(['agent', 'super_admin'])
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge(),
                Tables\Columns\TextColumn::make('department.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedAgent.name')
                    ->label('Agent')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sla_status')
                    ->label('SLA')
                    ->badge()
                    ->getStateUsing(fn (Ticket $record): string => $record->sla_status)
                    ->color(fn (string $state): string => match ($state) {
                        'on_track' => 'success',
                        'at_risk' => 'warning',
                        'breached' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'on_track' => 'On Track',
                        'at_risk' => 'At Risk',
                        'breached' => 'Breached',
                        default => 'No SLA',
                    }),
                Tables\Columns\TextColumn::make('first_response_due_at')
                    ->label('Response Due')
                    ->dateTime('M d, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('resolution_due_at')
                    ->label('Resolution Due')
                    ->dateTime('M d, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TicketStatus::class),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(TicketPriority::class),
                Tables\Filters\SelectFilter::make('department')
                    ->relationship('department', 'name'),
                Tables\Filters\TernaryFilter::make('sla_breached')
                    ->label('SLA Breached')
                    ->queries(
                        true: fn (Tables\Filters\TernaryFilter $filter, $query) => $query->where(function ($q) {
                            $q->where('first_response_breached', true)
                                ->orWhere('resolution_breached', true);
                        }),
                        false: fn (Tables\Filters\TernaryFilter $filter, $query) => $query->where('first_response_breached', false)
                            ->where('resolution_breached', false),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            TicketRepliesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
            'view' => Pages\ViewTicket::route('/{record}'),
        ];
    }
}
