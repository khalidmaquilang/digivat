<?php

declare(strict_types=1);

namespace Features\UserProfile\Enums;

use Features\Shared\Enums\EnumArrayTrait;
use Filament\Support\Contracts\HasLabel;

enum SpouseEmploymentStatusEnum: string implements HasLabel
{
    use EnumArrayTrait;

    case Unemployed = 'unemployed';
    case EmployedLocally = 'employed_locally';

    case EmployedAbroad = 'employed_abroad';
    case BusinessPracticeProfession = 'business_practice_profession';

    public function getLabel(): string
    {
        return match ($this) {
            self::Unemployed => 'Unemployed',
            self::EmployedLocally => 'Employed Locally',
            self::EmployedAbroad => 'Employed Abroad',
            self::BusinessPracticeProfession => 'Engaged in Business/Practice of Profession',
        };
    }
}
