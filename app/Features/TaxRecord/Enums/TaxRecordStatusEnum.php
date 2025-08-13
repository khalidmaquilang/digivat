<?php

declare(strict_types=1);

namespace Features\TaxRecord\Enums;

use Features\Shared\Enums\EnumArrayTrait;
use Filament\Support\Contracts\HasLabel;

enum TaxRecordStatusEnum: string implements HasLabel
{
    use EnumArrayTrait;

    case Acknowledged = 'acknowledged';
    case Paid = 'paid';
    case Expired = 'expired';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return $this->name;
    }
}
