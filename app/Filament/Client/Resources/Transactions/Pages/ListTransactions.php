<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\Transactions\Pages;

use App\Filament\Client\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;
}
