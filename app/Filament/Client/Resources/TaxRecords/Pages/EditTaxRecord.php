<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Pages;

use App\Filament\Client\Resources\TaxRecords\TaxRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTaxRecord extends EditRecord
{
    protected static string $resource = TaxRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
