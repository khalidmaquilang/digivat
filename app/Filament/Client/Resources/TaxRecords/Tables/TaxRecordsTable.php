<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Tables;

use App\Features\TaxRecord\Actions\BulkCancelTaxRecordAction;
use App\Features\TaxRecord\Actions\CancelTaxRecordAction;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class TaxRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(TaxRecord::tableSchema())
            ->filters([
                DateRangeFilter::make('sales_date'),
                SelectFilter::make('status')
                    ->options(TaxRecordStatusEnum::class),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('View Receipt')
                        ->icon(LucideIcon::Receipt)
                        ->color('primary')
                        ->url(fn (TaxRecord $record) => route('bir-receipt.show', $record)),
                    Action::make('cancel')
                        ->label('Cancel')
                        ->icon(LucideIcon::XCircle)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Cancel Tax Record')
                        ->modalDescription('Are you sure you want to cancel this tax record? This action cannot be undone.')
                        ->action(fn ($record) => app(CancelTaxRecordAction::class)->handle($record))
                        ->visible(fn ($record): bool => $record->status !== TaxRecordStatusEnum::Cancelled),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_cancel')
                        ->label('Cancel Selected')
                        ->icon(LucideIcon::XCircle)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Cancel Tax Records')
                        ->modalDescription('Are you sure you want to cancel the selected tax records? This action cannot be undone.')
                        ->action(fn (Collection $records) => app(BulkCancelTaxRecordAction::class)->handle($records)),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
