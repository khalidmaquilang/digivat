<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\BalanceTransactions;

use App\Features\BalanceTransaction\Models\BalanceTransaction;
use App\Features\Business\Models\Business;
use App\Features\Partner\Actions\CheckPartnerAction;
use App\Filament\Client\Resources\BalanceTransactions\Pages\ListBalanceTransactions;
use App\Filament\Client\Resources\BalanceTransactions\Schemas\BalanceTransactionForm;
use App\Filament\Client\Resources\BalanceTransactions\Tables\BalanceTransactionsTable;
use BackedEnum;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BalanceTransactionResource extends Resource
{
    protected static ?string $model = BalanceTransaction::class;

    protected static ?string $modelLabel = 'Wallet Transactions';

    protected static ?string $slug = 'wallet-transactions';

    protected static string|BackedEnum|null $navigationIcon = LucideIcon::Wallet;

    public static function canAccess(): bool
    {
        /** @var ?Business $business */
        $business = Filament::getTenant();
        if ($business === null) {
            return false;
        }

        return app(CheckPartnerAction::class)->handle($business->id);
    }

    public static function form(Schema $schema): Schema
    {
        return BalanceTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BalanceTransactionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBalanceTransactions::route('/'),
        ];
    }
}
