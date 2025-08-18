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
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
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
                        ->schema([
                            Textarea::make('cancel_reason')
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->action(function (TaxRecord $record, array $data): void {
                            /** @var ?string $cancel_reason */
                            $cancel_reason = $data['cancel_reason'] ?? null;
                            if ($cancel_reason === null) {
                                return;
                            }

                            app(CancelTaxRecordAction::class)->handle($record, $data['cancel_reason']);

                            Notification::make()
                                ->title('Tax record cancelled successfully!')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (TaxRecord $record): bool => $record->status === TaxRecordStatusEnum::Acknowledged),
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
                        ->schema([
                            Textarea::make('cancel_reason')
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            /** @var ?string $cancel_reason */
                            $cancel_reason = $data['cancel_reason'] ?? null;
                            if ($cancel_reason === null) {
                                return;
                            }

                            app(BulkCancelTaxRecordAction::class)->handle($records, $cancel_reason);

                            Notification::make()
                                ->title('Tax records cancelled successfully!')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
