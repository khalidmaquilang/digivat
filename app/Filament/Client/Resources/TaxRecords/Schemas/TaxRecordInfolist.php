<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Schemas;

use App\Filament\Components\Infolists\MoneyEntry\MoneyEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class TaxRecordInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
