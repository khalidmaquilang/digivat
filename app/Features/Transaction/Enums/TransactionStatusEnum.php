<?php

declare(strict_types=1);

namespace App\Features\Transaction\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TransactionStatusEnum: string implements HasColor, HasLabel
{
    case Completed = 'completed';
    case Pending = 'pending';
    case Failed = 'failed';
    case Cancelled = 'cancelled';

    public function getColor(): string
    {
        return match ($this) {
            self::Completed => 'success',
            self::Pending => 'info',
            self::Failed => 'danger',
            self::Cancelled => 'danger',
        };
    }

    public function getLabel(): string
    {
        return $this->name;
    }
}
