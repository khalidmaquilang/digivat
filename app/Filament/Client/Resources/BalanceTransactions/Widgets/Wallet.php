<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\BalanceTransactions\Widgets;

use App\Features\Business\Models\Business;
use App\Features\Shared\Helpers\MoneyHelper;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Wallet extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        /** @var ?Business $business */
        $business = Filament::getTenant();
        abort_if($business === null, 403);

        return [
            Stat::make('Wallet', MoneyHelper::currency($business->getBalance())),
        ];
    }
}
