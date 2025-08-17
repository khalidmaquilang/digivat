<?php

declare(strict_types=1);

namespace App\Filament\Resources\CreativeDomains\Pages;

use App\Filament\Resources\CreativeDomains\CreativeDomainResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCreativeDomain extends CreateRecord
{
    protected static string $resource = CreativeDomainResource::class;
}
