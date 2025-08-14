<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\RelationManagers;

use App\Filament\Components\TableColumns\MoneyColumn\MoneyColumn;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaxRecordItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'taxRecordItems';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item_name')
                    ->searchable(),
                TextColumn::make('quantity'),
                MoneyColumn::make('unit_price'),
                MoneyColumn::make('discount_amount'),
                MoneyColumn::make('total'),
            ]);
    }
}
