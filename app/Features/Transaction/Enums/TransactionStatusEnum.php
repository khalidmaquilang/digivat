<?php

declare(strict_types=1);

namespace App\Features\Transaction\Enums;

enum TransactionStatusEnum: string
{
    case Completed = 'completed';
    case Pending = 'pending';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
}
