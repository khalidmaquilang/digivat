<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Tables;

use App\Filament\Components\TableColumns\MoneyColumn\MoneyColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class TaxRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Receipt id')
                    ->searchable(),
                TextColumn::make('sales_date')
                    ->dateTime()
                    ->sortable(),
                MoneyColumn::make('total_amount'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('transaction_reference')
                    ->searchable(),
                MoneyColumn::make('gross_amount'),
                MoneyColumn::make('order_discount'),
                MoneyColumn::make('taxable_amount'),
                MoneyColumn::make('tax_amount'),
                TextColumn::make('category_type')
                    ->badge(),
                TextColumn::make('valid_until')
                    ->date(),
            ])
            ->filters([
                DateRangeFilter::make('sales_date'),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
