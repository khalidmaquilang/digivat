<?php

declare(strict_types=1);

namespace App\Features\Business\Models\Traits;

use App\Features\Business\Models\Business;
use Filament\Facades\Filament;
use http\Exception\RuntimeException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasBusinessTrait
{
    /**
     * The "booted" method of the model.
     */
    protected static function bootedBusiness(): void
    {
        static::addGlobalScope('only_business', function (Builder $builder): void {
            /** @var ?Business $business */
            $business = Filament::getTenant();
            if ($business === null) {
                return;
            }

            $builder->where('business_id', $business->id);
        });

        static::creating(function (Model $model): void {
            /** @var ?Business $business */
            $business = Filament::getTenant();
            if ($business === null) {
                return;
            }

            if (! property_exists($model, 'business_id')) {
                throw new RuntimeException('Model must have business_id property');
            }

            $model->business_id = $business->id;
        });
    }

    /**
     * @return BelongsTo<Business, $this>
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
