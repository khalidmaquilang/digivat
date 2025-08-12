<?php

declare(strict_types=1);

namespace Features\UserProfile\Enums;

use Features\Shared\Enums\EnumArrayTrait;

enum BirInvoiceMannerTypeEnum: string
{
    use EnumArrayTrait;

    case Bound = 'bound';
    case LooseLeaf = 'loose_leaf';
}
