<?php

declare(strict_types=1);

namespace App\Filament\Resources\Businesses\Schemas;

use App\Features\Business\Models\Business;
use Filament\Schemas\Schema;

class BusinessForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(Business::schema());
    }
}
