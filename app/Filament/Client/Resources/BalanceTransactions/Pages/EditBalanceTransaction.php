<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\BalanceTransactions\Pages;

use App\Filament\Client\Resources\BalanceTransactions\BalanceTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBalanceTransaction extends EditRecord
{
    protected static string $resource = BalanceTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
