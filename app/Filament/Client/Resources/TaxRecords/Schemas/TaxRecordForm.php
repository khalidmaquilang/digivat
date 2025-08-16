<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Schemas;

use App\Features\TaxRecord\Enums\CategoryTypeEnum;
use App\Filament\Components\Fields\MoneyInput\MoneyInput;
use App\Filament\Components\Fields\NumericInput\NumericInput;
use App\Filament\Components\Fields\TextInput\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class TaxRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('sales_date')
                    ->required(),
                Select::make('category_type')
                    ->options(CategoryTypeEnum::class)
                    ->required(),
                TextInput::make('transaction_reference')
                    ->columnSpanFull(),
                Fieldset::make('Items')
                    ->schema([
                        Repeater::make('taxRecordItems')
                            ->columnSpanFull()
                            ->relationship('taxRecordItems')
                            ->schema([
                                TextInput::make('item_name')
                                    ->lazy()
                                    ->required(),
                                Group::make([
                                    MoneyInput::make('unit_price')
                                        ->minValue(0)
                                        ->default(0)
                                        ->required()
                                        ->live(debounce: 500)
                                        ->partiallyRenderComponentsAfterStateUpdated(['total_amount'])
                                        ->afterStateUpdated(function (Get $get, Set $set): void {
                                            /** @var float $unit_price */
                                            $unit_price = $get('unit_price') ?? 0;

                                            /** @var int $quantity */
                                            $quantity = $get('quantity') ?? 0;

                                            /** @var float $discount_amount */
                                            $discount_amount = $get('discount_amount') ?? 0;

                                            $set('total_amount', $unit_price * $quantity - $discount_amount);
                                        }),
                                    NumericInput::make('quantity')
                                        ->prefix('₱')
                                        ->minValue(1)
                                        ->default(1)
                                        ->required()
                                        ->live(debounce: 500)
                                        ->partiallyRenderComponentsAfterStateUpdated(['total_amount'])
                                        ->afterStateUpdated(function (Get $get, Set $set): void {
                                            /** @var float $unit_price */
                                            $unit_price = $get('unit_price') ?? 0;

                                            /** @var int $quantity */
                                            $quantity = $get('quantity') ?? 0;

                                            /** @var float $discount_amount */
                                            $discount_amount = $get('discount_amount') ?? 0;

                                            $set('total_amount', $unit_price * $quantity - $discount_amount);
                                        }),
                                    MoneyInput::make('discount_amount')
                                        ->default(0)
                                        ->minValue(0)
                                        ->required()
                                        ->live(debounce: 500)
                                        ->partiallyRenderComponentsAfterStateUpdated(['total_amount'])
                                        ->afterStateUpdated(function (Get $get, Set $set): void {
                                            /** @var float $unit_price */
                                            $unit_price = $get('unit_price') ?? 0;

                                            /** @var int $quantity */
                                            $quantity = $get('quantity') ?? 0;

                                            /** @var float $discount_amount */
                                            $discount_amount = $get('discount_amount') ?? 0;

                                            $set('total_amount', $unit_price * $quantity - $discount_amount);
                                        }),
                                    MoneyInput::make('total_amount')
                                        ->disabled(),
                                ])
                                    ->columns(4),
                            ])
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['item_name'] ?? null),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
