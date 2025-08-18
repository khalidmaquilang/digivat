<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Pages;

use App\Filament\Client\Resources\TaxRecords\Actions\RemitTaxButton;
use App\Filament\Client\Resources\TaxRecords\RelationManagers\TaxRecordItemsRelationManager;
use App\Filament\Client\Resources\TaxRecords\TaxRecordResource;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Actions\Action;
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
            RemitTaxButton::make(),
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
