<?php

declare(strict_types=1);

namespace Features\UserProfile\Enums;

use Features\Shared\Enums\EnumArrayTrait;

enum BirInvoiceTypeEnum: string
{
    use EnumArrayTrait;

    case VAT = 'vat';
    case NonVAT = 'non_vat';
}
