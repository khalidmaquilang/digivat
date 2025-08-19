<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\BalanceTransactions\Pages;

use App\Filament\Client\Resources\BalanceTransactions\BalanceTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBalanceTransactions extends ListRecords
{
    protected static string $resource = BalanceTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
