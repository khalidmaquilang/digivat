<?php

declare(strict_types=1);

namespace Features\UserProfile\Enums;

use Features\Shared\Enums\EnumArrayTrait;

enum AddressTypeEnum: string
{
    use EnumArrayTrait;

    case Residence = 'residence';
    case PlaceOfBusiness = 'place_of_business';

    case EmployerAddress = 'employer_address';
}
