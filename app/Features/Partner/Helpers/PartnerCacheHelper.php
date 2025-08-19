<?php

declare(strict_types=1);

namespace App\Features\Partner\Helpers;

use App\Features\Partner\Models\Partner;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class PartnerCacheHelper
{
    const CACHE_KEY = 'partners';

    const CACHE_TTL = 60;

    /**
     * @return Collection<Partner>
     */
    public static function get(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn () => Partner::with('business', 'business.wallet')->get());
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function flush(): void
    {
        Cache::delete(self::CACHE_KEY);
    }
}
