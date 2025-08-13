<?php

declare(strict_types=1);

namespace Features\TaxRecord\Enums;

use Features\Shared\Enums\EnumArrayTrait;

enum CalculateTaxRecordModeEnum: string
{
    use EnumArrayTrait;

    case Preview = 'preview';

    case Acknowledge = 'acknowledge';
}
