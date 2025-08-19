<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\BalanceTransactions\Pages;

use App\Filament\Client\Resources\BalanceTransactions\BalanceTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBalanceTransaction extends CreateRecord
{
    protected static string $resource = BalanceTransactionResource::class;
}
