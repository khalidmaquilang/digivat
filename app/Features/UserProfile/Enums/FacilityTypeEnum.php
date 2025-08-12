<?php

declare(strict_types=1);

namespace Features\UserProfile\Enums;

use Features\Shared\Enums\EnumArrayTrait;
use Filament\Support\Contracts\HasLabel;

enum FacilityTypeEnum: string implements HasLabel
{
    use EnumArrayTrait;

    case PP = 'pp';
    case SP = 'sp';
    case WH = 'wh';
    case SR = 'sr';
    case GG = 'gg';
    case BT = 'bt';
    case RP = 'rp';
    case Others = 'others';

    public function getLabel(): string
    {
        return match ($this) {
            self::PP => 'PP-Place of Production/Plant',
            self::SP => 'SP-Storage Place',
            self::WH => 'WH-Warehouse',
            self::SR => 'SR-Showroom',
            self::GG => 'GG-Garage',
            self::BT => 'BT-Bus Terminal',
            self::RP => 'RP-Real Property for Lease with No Sales Activity',
            self::Others => 'Others',
        };
    }
}
