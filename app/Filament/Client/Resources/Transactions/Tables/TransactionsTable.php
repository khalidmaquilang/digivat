<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\Transactions\Tables;

use App\Filament\Components\TableColumns\MoneyColumn\MoneyColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Transaction id')
                    ->searchable(),
                TextColumn::make('transaction_date')
                    ->dateTime(),
                MoneyColumn::make('amount'),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('tax_record_id')
                    ->searchable(),
            ])
            ->filters([
                DateRangeFilter::make('transaction_date'),
            ], layout: FiltersLayout::AboveContent)
            ->defaultSort('transaction_date', 'desc');
    }
}
