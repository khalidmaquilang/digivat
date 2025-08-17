<?php

declare(strict_types=1);

namespace App\Filament\Resources\CreativeDomains\Pages;

use App\Filament\Resources\CreativeDomains\CreativeDomainResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCreativeDomains extends ListRecords
{
    protected static string $resource = CreativeDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
