<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\BalanceTransactions\Tables;

use App\Filament\Components\TableColumns\MoneyColumn\MoneyColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class BalanceTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('Wallet transaction id')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Transaction date'),
                MoneyColumn::make('amount_float')
                    ->label('Amount'),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('meta')
                    ->label('Transaction reference')
                    ->searchable(),
            ])
            ->filters([
                DateRangeFilter::make('created_at')
                    ->label('Transaction date'),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('created_at', 'desc');
    }
}
