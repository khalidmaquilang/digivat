<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Enums;

use App\Features\Shared\Enums\EnumArrayTrait;

enum CalculateTaxRecordModeEnum: string
{
    use EnumArrayTrait;

    case Preview = 'preview';

    case Acknowledge = 'acknowledge';
}
