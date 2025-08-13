<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Pages;

use App\Filament\Client\Resources\TaxRecords\TaxRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaxRecord extends CreateRecord
{
    protected static string $resource = TaxRecordResource::class;
}
