<?php

declare(strict_types=1);

namespace Features\TaxRecord\Enums;

use Features\Shared\Enums\EnumArrayTrait;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TaxRecordStatusEnum: string implements HasColor, HasLabel
{
    use EnumArrayTrait;

    case Preview = 'preview';
    case Acknowledged = 'acknowledged';
    case Paid = 'paid';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Preview => 'gray',
            self::Acknowledged => 'info',
            self::Paid => 'success',
            self::Expired => 'danger',
            self::Cancelled => 'danger',
        };
    }
}
