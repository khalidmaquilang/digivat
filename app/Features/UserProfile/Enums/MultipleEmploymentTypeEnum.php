<?php

declare(strict_types=1);

namespace Features\UserProfile\Enums;

use Features\Shared\Enums\EnumArrayTrait;
use Filament\Support\Contracts\HasLabel;

enum MultipleEmploymentTypeEnum: string implements HasLabel
{
    use EnumArrayTrait;

    case SuccessiveEmployments = 'successive_employments';
    case ConcurrentEmployments = 'concurrent_employments';

    public function getLabel(): string
    {
        return match ($this) {
            self::SuccessiveEmployments => 'Successive Employments (With previous employer/s within the calendar year)',
            self::ConcurrentEmployments => 'Concurrent Employments (With two or more employers at the same time within the calendar year',
        };
    }
}
