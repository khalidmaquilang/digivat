<?php

declare(strict_types=1);

namespace App\Filament\Client\Pages\Tenancy;

use App\Features\Business\Models\Business;
use App\Features\User\Models\User;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;

class RegisterBusiness extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register business';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components(Business::schema());
    }

    protected function handleRegistration(array $data): Business
    {
        /** @var ?User $user */
        $user = auth()->user();
        abort_if($user === null, 404);

        $data['owner_id'] = $user->id;

        $business = Business::create($data);

        $business->members()->attach($user->id);

        return $business;
    }
}
