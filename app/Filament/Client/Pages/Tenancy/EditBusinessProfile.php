<?php

declare(strict_types=1);

namespace App\Filament\Client\Pages\Tenancy;

use App\Features\Business\Models\Business;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;

class EditBusinessProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Business profile';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(Business::schema(disable_tin: true));
    }
}
