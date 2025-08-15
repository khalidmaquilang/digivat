<?php

declare(strict_types=1);

namespace App\Features\Shared\Enums;

enum QueueEnum: string
{
    use EnumArrayTrait;

    case ShortRunning = 'short_running';
    case LongRunning = 'long_running';
}
