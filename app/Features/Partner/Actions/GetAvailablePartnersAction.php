<?php

declare(strict_types=1);

namespace App\Features\Partner\Actions;

use App\Features\Partner\Helpers\PartnerCacheHelper;
use App\Features\Partner\Models\Partner;
use Illuminate\Database\Eloquent\Collection;

class GetAvailablePartnersAction
{
    /**
     * @return Collection<Partner>
     */
    public function handle(): Collection
    {
        return PartnerCacheHelper::get();
    }
}
