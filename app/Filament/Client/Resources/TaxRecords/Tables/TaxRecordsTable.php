<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Tables;

use App\Filament\Components\Summarizes\Sum;
use App\Filament\Components\TableColumns\MoneyColumn\MoneyColumn;
use Features\TaxRecord\Enums\TaxRecordStatusEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
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
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('transaction_reference')
                    ->searchable(),
                MoneyColumn::make('gross_amount')
                    ->summarize(Sum::make()),
                MoneyColumn::make('order_discount')
                    ->summarize(Sum::make()),
                MoneyColumn::make('taxable_amount')
                    ->summarize(Sum::make()),
                MoneyColumn::make('tax_amount')
                    ->summarize(Sum::make()),
                MoneyColumn::make('total_amount')
                    ->summarize(Sum::make()),
                TextColumn::make('category_type')
                    ->badge(),
                TextColumn::make('valid_until')
                    ->date(),
            ])
            ->filters([
                DateRangeFilter::make('sales_date'),
                SelectFilter::make('status')
                    ->options(TaxRecordStatusEnum::class),
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
