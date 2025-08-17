<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\Users\Schemas;

use App\Features\Business\Models\Business;
use App\Filament\Components\Fields\TextInput\TextInput;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule) {
                        /** @var ?Business $business */
                        $business = Filament::getTenant();
                        if ($business === null) {
                            return $rule;
                        }

                        return $rule->where('business_id', $business->id);
                    }),
            ]);
    }
}
