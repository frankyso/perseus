<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TicketStatus: string implements HasColor, HasIcon, HasLabel
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case WaitingReply = 'waiting_reply';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In Progress',
            self::WaitingReply => 'Waiting Reply',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Open => 'warning',
            self::InProgress => 'info',
            self::WaitingReply => 'gray',
            self::Resolved => 'success',
            self::Closed => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Open => 'heroicon-m-envelope-open',
            self::InProgress => 'heroicon-m-arrow-path',
            self::WaitingReply => 'heroicon-m-clock',
            self::Resolved => 'heroicon-m-check-circle',
            self::Closed => 'heroicon-m-x-circle',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this, [self::Open, self::InProgress, self::WaitingReply]);
    }
}
