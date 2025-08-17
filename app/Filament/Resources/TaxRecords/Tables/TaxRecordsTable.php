<?php

declare(strict_types=1);

namespace App\Filament\Resources\TaxRecords\Tables;

use App\Features\TaxRecord\Models\TaxRecord;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TaxRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(array_merge([
                TextColumn::make('business.name')
                    ->searchable(),
            ], TaxRecord::tableSchema()))
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    Action::make('View Receipt')
                        ->icon(LucideIcon::Receipt)
                        ->color('primary')
                        ->url(fn (TaxRecord $record) => route('bir-receipt.show', $record)),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
