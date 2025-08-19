<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\BalanceTransactions\Schemas;

use Filament\Schemas\Schema;

class BalanceTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
