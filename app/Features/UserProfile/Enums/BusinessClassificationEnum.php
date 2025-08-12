<?php

declare(strict_types=1);

namespace Features\UserProfile\Enums;

use Features\Shared\Enums\EnumArrayTrait;

enum BusinessClassificationEnum: string
{
    use EnumArrayTrait;

    case Micro = 'micro';
    case Small = 'small';

    case Medium = 'medium';
    case Large = 'large';
}
