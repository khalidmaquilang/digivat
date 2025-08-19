<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\Transactions;

use App\Features\Transaction\Models\Transaction;
use App\Filament\Client\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Client\Resources\Transactions\Tables\TransactionsTable;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = LucideIcon::HandCoins;

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
        ];
    }
}
