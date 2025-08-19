<?php

declare(strict_types=1);

namespace App\Filament\Resources\Partners\Schemas;

use App\Features\Partner\Enums\PartnerShareTypeEnum;
use App\Filament\Components\Fields\NumericInput\NumericInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('business_id')
                            ->relationship('business', 'name')
                            ->preload()
                            ->optionsLimit(10)
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->searchable(),
                        FusedGroup::make([
                            NumericInput::make('shares')
                                ->minValue(0)
                                ->required()
                                ->maxValue(fn (Get $get): ?int => $get('share_type') === PartnerShareTypeEnum::Percentage ? 100 : null),
                            Select::make('share_type')
                                ->options(PartnerShareTypeEnum::class)
                                ->default(PartnerShareTypeEnum::Percentage)
                                ->required(),
                        ])
                            ->label('Shares')
                            ->columns(2),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
