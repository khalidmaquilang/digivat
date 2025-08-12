<?php

declare(strict_types=1);

namespace Features\UserProfile\Enums;

use Features\Shared\Enums\EnumArrayTrait;

enum CivilStatusEnum: string
{
    use EnumArrayTrait;

    case Single = 'single';
    case Married = 'married';

    case Widowed = 'widowed';
    case LegallySeparated = 'legally_separated';
}
