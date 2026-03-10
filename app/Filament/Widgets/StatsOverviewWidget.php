<?php

namespace App\Filament\Widgets;

use App\Models\KnowledgebaseArticle;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Tickets', Ticket::query()->count()),
            Stat::make('Open Tickets', Ticket::query()->where('status', 'open')->count()),
            Stat::make('Resolved Tickets', Ticket::query()->where('status', 'resolved')->count()),
            Stat::make('KB Articles', KnowledgebaseArticle::query()->count()),
        ];
    }
}
