<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\BalanceTransactions\Pages;

use App\Filament\Client\Resources\BalanceTransactions\BalanceTransactionResource;
use App\Filament\Client\Resources\BalanceTransactions\Widgets\Wallet;
use Filament\Resources\Pages\ListRecords;

class ListBalanceTransactions extends ListRecords
{
    protected static string $resource = BalanceTransactionResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            Wallet::class,
        ];
    }
}
