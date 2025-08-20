<?php

declare(strict_types=1);

namespace App\Features\Partner\Enums;

use App\Features\Shared\Enums\EnumArrayTrait;
use Filament\Support\Contracts\HasLabel;

enum PartnerShareTypeEnum: string implements HasLabel
{
    use EnumArrayTrait;

    case Percentage = 'percentage';

    case Fixed = 'fixed';

    public function getLabel(): string
    {
        return $this->name;
    }
}
