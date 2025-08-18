<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Schemas;

use App\Features\Shared\Helpers\MoneyHelper;
use App\Features\TaxRecord\Actions\GetTaxByCategoryAction;
use App\Features\TaxRecord\Actions\GetTotalGrossAmountAction;
use App\Features\TaxRecord\Enums\CategoryTypeEnum;
use App\Filament\Components\Fields\IntegerInput\IntegerInput;
use App\Filament\Components\Fields\MoneyInput\MoneyInput;
use App\Filament\Components\Fields\TextInput\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;

class TaxRecordForm
{
    protected static function computeTotalAmount(Get $get, Set $set): void
    {
        /** @var float $unit_price */
        $unit_price = $get('unit_price') ?? 0;

        /** @var int $quantity */
        $quantity = $get('quantity') ?? 0;

        /** @var float $discount_amount */
        $discount_amount = $get('discount_amount') ?? 0;

        $set('total_amount', MoneyHelper::currency($unit_price * $quantity - $discount_amount));
    }

    public static function configure(Schema $schema): Schema
    {
        $sub_total = 0;
        $taxable_amount = 0;
        $tax_amount = 0;

        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make([
                            DatePicker::make('sales_date')
                                ->maxDate(now())
                                ->required(),
                            Select::make('category_type')
                                ->options(CategoryTypeEnum::class)
                                ->required()
                                ->live(),
                        ])
                            ->columns(2),
                        TextInput::make('transaction_reference')
                            ->required()
                            ->columnSpanFull(),
                        Fieldset::make('Items')
                            ->schema([
                                Repeater::make('taxRecordItems')
                                    ->label('Items')
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
                                                ->afterStateUpdated(fn (Get $get, Set $set) => static::computeTotalAmount($get, $set)),
                                            IntegerInput::make('quantity')
                                                ->minValue(1)
                                                ->default(1)
                                                ->required()
                                                ->live(debounce: 500)
                                                ->afterStateUpdated(fn (Get $get, Set $set) => static::computeTotalAmount($get, $set)),
                                            MoneyInput::make('discount_amount')
                                                ->default(0)
                                                ->minValue(0)
                                                ->required()
                                                ->live(debounce: 500)
                                                ->afterStateUpdated(fn (Get $get, Set $set) => static::computeTotalAmount($get, $set)),
                                            TextInput::make('total_amount')
                                                ->default(0)
                                                ->disabled(),
                                        ])
                                            ->columns(4),
                                    ])
                                    ->dehydrated(true)
                                    ->required()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['item_name'] ?? null),
                                MoneyInput::make('order_discount')
                                    ->default(0)
                                    ->minValue(0)
                                    ->live(debounce: 500),
                            ])
                            ->columnSpanFull(),
                        Fieldset::make('Totals')
                            ->schema([
                                Text::make(function (Get $get) use (&$sub_total): string {

                                    /** @var array<string, mixed> $tax_items */
                                    $tax_items = $get('taxRecordItems') ?? [];

                                    $sub_total = app(GetTotalGrossAmountAction::class)->handle($tax_items);

                                    return 'Gross Amount: '.MoneyHelper::currency($sub_total);
                                })
                                    ->size(TextSize::Large),
                                Text::make(function (Get $get) use (&$sub_total, &$taxable_amount): string {
                                    /** @var float $order_discount */
                                    $order_discount = $get('order_discount') ?? 0;

                                    $taxable_amount = $sub_total - $order_discount;

                                    return 'Taxable Amount: '.MoneyHelper::currency($taxable_amount);
                                })
                                    ->size(TextSize::Large),
                                Text::make(function (Get $get) use (&$taxable_amount, &$tax_amount): string {
                                    /** @var ?CategoryTypeEnum $category_type */
                                    $category_type = $get('category_type') ?? null;

                                    $tax_amount = 0;

                                    if ($category_type === null) {
                                        return 'Tax: '.MoneyHelper::currency($tax_amount);
                                    }

                                    $tax_amount = app(GetTaxByCategoryAction::class)->handle($category_type, $taxable_amount);

                                    return 'Tax: '.MoneyHelper::currency($tax_amount);
                                })
                                    ->size(TextSize::Large),
                                Text::make(function () use (&$taxable_amount, &$tax_amount): string {
                                    return 'Total Amount: '.MoneyHelper::currency($taxable_amount + $tax_amount);
                                })
                                    ->size(TextSize::Large),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
