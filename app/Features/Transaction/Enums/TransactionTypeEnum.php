<?php

declare(strict_types=1);

namespace App\Features\Transaction\Enums;

enum TransactionTypeEnum: string
{
    case TaxRemittance = 'tax_remittance';
    case Refund = 'refund';
    case Adjustment = 'adjustment';
    case Penalty = 'penalty';
    case Interest = 'interest';
}
