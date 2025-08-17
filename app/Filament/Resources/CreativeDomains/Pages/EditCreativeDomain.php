<?php

declare(strict_types=1);

namespace App\Filament\Resources\CreativeDomains\Pages;

use App\Filament\Resources\CreativeDomains\CreativeDomainResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCreativeDomain extends EditRecord
{
    protected static string $resource = CreativeDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
