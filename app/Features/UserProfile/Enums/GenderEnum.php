<?php

declare(strict_types=1);

namespace Features\UserProfile\Enums;

use Features\Shared\Enums\EnumArrayTrait;

enum GenderEnum: string
{
    use EnumArrayTrait;

    case Male = 'male';
    case Female = 'female';
}
