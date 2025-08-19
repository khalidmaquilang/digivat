<?php

declare(strict_types=1);

namespace App\Features\Transaction\Enums;

use Filament\Support\Contracts\HasLabel;

enum TransactionTypeEnum: string implements HasLabel
{
    case TaxRemittance = 'tax_remittance';
    case Refund = 'refund';
    case Adjustment = 'adjustment';
    case Penalty = 'penalty';

    public function getLabel(): string
    {
        return match ($this) {
            self::TaxRemittance => 'Tax Remittance',
            self::Refund => 'Refund',
            self::Adjustment => 'Adjustment',
            self::Penalty => 'Penalty',
        };
    }
}
