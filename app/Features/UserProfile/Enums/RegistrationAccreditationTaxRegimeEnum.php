<?php

declare(strict_types=1);

namespace Features\UserProfile\Enums;

use Features\Shared\Enums\EnumArrayTrait;

enum RegistrationAccreditationTaxRegimeEnum: string
{
    use EnumArrayTrait;

    case Regular = 'regular';
    case Special = 'special';

    case Exempt = 'exempt';
}
