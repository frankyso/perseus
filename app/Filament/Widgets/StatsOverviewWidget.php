<?php

namespace App\Filament\Widgets;

use App\Enums\TicketStatus;
use App\Models\KnowledgebaseArticle;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $slaBreached = Ticket::query()
            ->where(function ($query) {
                $query->where('first_response_breached', true)
                    ->orWhere('resolution_breached', true);
            })
            ->count();

        return [
            Stat::make('Total Tickets', Ticket::query()->count()),
            Stat::make('Open Tickets', Ticket::query()->where('status', TicketStatus::Open)->count())
                ->color('warning'),
            Stat::make('Resolved Tickets', Ticket::query()->where('status', TicketStatus::Resolved)->count())
                ->color('success'),
            Stat::make('SLA Breached', $slaBreached)
                ->color($slaBreached > 0 ? 'danger' : 'success')
                ->description($slaBreached > 0 ? 'Tickets with SLA violations' : 'All tickets within SLA'),
            Stat::make('KB Articles', KnowledgebaseArticle::query()->count()),
        ];
    }
}
