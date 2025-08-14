<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Pages;

use App\Filament\Client\Resources\TaxRecords\RelationManagers\TaxRecordItemsRelationManager;
use App\Filament\Client\Resources\TaxRecords\TaxRecordResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTaxRecord extends ViewRecord
{
    protected static string $resource = TaxRecordResource::class;

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
