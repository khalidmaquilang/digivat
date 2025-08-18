<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Actions;

use App\Features\Shared\Helpers\MoneyHelper;
use App\Features\TaxRecord\Actions\UpdateTaxRecordStatusAction;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class RemitTaxButton extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'remitTax';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Remit Tax')
            ->color('success')
            ->requiresConfirmation()
            ->modalDescription(fn (TaxRecord $record): string => "Are you sure you'd like you want to remit ".MoneyHelper::currency($record->tax_amount).' to the tax authority?')
            ->icon(LucideIcon::Banknote)
            ->action(function (TaxRecord $record): void {
                app(UpdateTaxRecordStatusAction::class)->handle($record, TaxRecordStatusEnum::Paid);

                Notification::make()
                    ->title('Tax remitted successfully!')
                    ->success()
                    ->send();
            })
            ->visible(fn (TaxRecord $record): bool => $record->status === TaxRecordStatusEnum::Acknowledged || $record->status === TaxRecordStatusEnum::Expired);
    }
}
