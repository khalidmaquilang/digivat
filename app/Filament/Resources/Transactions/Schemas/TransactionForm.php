<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Schemas;

use App\Features\Transaction\Enums\TransactionStatusEnum;
use App\Features\Transaction\Enums\TransactionTypeEnum;
use App\Filament\Components\Fields\MoneyInput\MoneyInput;
use App\Filament\Components\Fields\TextInput\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('business_id')
                            ->relationship('business', 'name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->optionsLimit(10),
                        DateTimePicker::make('transaction_date')
                            ->required(),
                        MoneyInput::make('amount')
                            ->required()
                            ->minValue(0),
                        TextInput::make('reference_number')
                            ->required(),
                        Select::make('type')
                            ->required()
                            ->options(TransactionTypeEnum::class),
                        Select::make('status')
                            ->required()
                            ->options(TransactionStatusEnum::class),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

            ]);
    }
}
