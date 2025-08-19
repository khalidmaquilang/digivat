<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Models\Traits;

use App\Filament\Components\Infolists\MoneyEntry\MoneyEntry;
use App\Filament\Components\Summarizes\Sum;
use App\Filament\Components\TableColumns\MoneyColumn\MoneyColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;

trait TaxRecordSchemaTrait
{
    /**
     * @return array<int, TextColumn>
     *
     * @throws \Exception
     */
    public static function tableSchema(): array
    {
        return [
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
        ];
    }

    /**
     * @return array<int, mixed>
     *
     * @throws \Exception
     */
    public static function getInfolistSchema(): array
    {
        return [
            Grid::make()
                ->columns(3)
                ->schema([
                    Grid::make()
                        ->columns(1)
                        ->schema([
                            Section::make('📄 Basic Information')
                                ->description('Core tax record details')
                                ->schema([
                                    TextEntry::make('id')
                                        ->label('Receipt ID')
                                        ->copyable()
                                        ->copyMessage('Receipt ID copied')
                                        ->copyMessageDuration(1500)
                                        ->badge()
                                        ->color('primary')
                                        ->weight('medium')
                                        ->size(TextSize::Large),
                                    TextEntry::make('transaction_reference')
                                        ->label('Transaction Reference')
                                        ->copyable()
                                        ->copyMessage('Reference copied')
                                        ->copyMessageDuration(1500)
                                        ->color('gray'),
                                    TextEntry::make('sales_date')
                                        ->label('Sales Date')
                                        ->dateTime('M j, Y g:i A'),
                                    TextEntry::make('valid_until')
                                        ->label('Valid Until')
                                        ->date('M j, Y'),
                                ])
                                ->columns(2),
                            // === Financial Summary ===
                            Section::make('💰 Financial Summary')
                                ->description('Breakdown of amounts and calculations')
                                ->schema([
                                    Grid::make()
                                        ->columns(2)
                                        ->schema([
                                            MoneyEntry::make('gross_amount')
                                                ->label('Gross Amount'),
                                            MoneyEntry::make('order_discount')
                                                ->label('Order Discount')
                                                ->color('danger'),
                                            MoneyEntry::make('taxable_amount')
                                                ->label('Taxable Amount'),
                                            MoneyEntry::make('tax_amount')
                                                ->label('Tax Amount'),
                                        ]),
                                    MoneyEntry::make('total_amount')
                                        ->label('Total Amount')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->color('success')
                                        ->columnSpanFull(),
                                ]),
                        ])
                        ->columnSpan(2),
                    Section::make('🏷️ Status & Classification')
                        ->description('Current status and category')
                        ->schema([
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->size('lg'),
                            TextEntry::make('category_type')
                                ->label('Category Type')
                                ->badge()
                                ->size('lg')
                                ->color('info'),
                            TextEntry::make('cancel_reason')
                                ->badge()
                                ->size('lg')
                                ->color('danger')
                                ->placeholder('-'),
                        ]),
                ])
                ->columnSpanFull(),
        ];
    }
}
