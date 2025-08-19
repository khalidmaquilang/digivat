<?php

declare(strict_types=1);

namespace App\Features\Partner\Actions;

use App\Features\Partner\Models\Partner;

class CheckPartnerAction
{
    public function handle(string $business_id): bool
    {
        return Partner::where('business_id', $business_id)->exists();
    }
}
