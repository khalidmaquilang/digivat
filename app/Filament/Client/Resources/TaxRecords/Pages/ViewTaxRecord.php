<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Pages;

use App\Filament\Client\Resources\TaxRecords\TaxRecordResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTaxRecord extends ViewRecord
{
    protected static string $resource = TaxRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
