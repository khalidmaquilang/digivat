<?php

declare(strict_types=1);

namespace App\Filament\Resources\TaxRecords\Pages;

use App\Filament\Resources\TaxRecords\TaxRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaxRecords extends ListRecords
{
    protected static string $resource = TaxRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
