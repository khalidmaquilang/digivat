<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Pages;

use App\Features\Shared\Helpers\MoneyHelper;
use App\Features\TaxRecord\Actions\UpdateTaxRecordStatusAction;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Filament\Client\Resources\TaxRecords\RelationManagers\TaxRecordItemsRelationManager;
use App\Filament\Client\Resources\TaxRecords\TaxRecordResource;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTaxRecord extends ViewRecord
{
    protected static string $resource = TaxRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('View Receipt')
                ->icon(LucideIcon::Receipt)
                ->url(route('bir-receipt.show', $this->record)),
            Action::make('Remit Tax')
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription("Are you sure you'd like you want to remit ".MoneyHelper::currency($this->record->tax_amount).' to the tax authority?')
                ->icon(LucideIcon::Banknote)
                ->action(function (TaxRecord $record): void {
                    app(UpdateTaxRecordStatusAction::class)->handle($record, TaxRecordStatusEnum::Paid);

                    Notification::make()
                        ->title('Tax remitted successfully!')
                        ->success()
                        ->send();
                })
                ->visible(fn (TaxRecord $record): bool => $record->status === TaxRecordStatusEnum::Acknowledged || $record->status === TaxRecordStatusEnum::Expired),
        ];
    }

    /**
     * @return array<int, string>
     */
    public function getRelationManagers(): array
    {
        return [
            TaxRecordItemsRelationManager::class,
        ];
    }
}
