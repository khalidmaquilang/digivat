<?php

declare(strict_types=1);

namespace App\Features\Partner\Models;

use App\Features\Business\Models\Business;
use App\Features\Partner\Enums\PartnerShareTypeEnum;
use App\Features\Partner\Helpers\PartnerCacheHelper;
use Database\Factories\PartnerFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $business_id
 * @property float $shares
 * @property PartnerShareTypeEnum $share_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Business $business
 *
 * @method static \Database\Factories\PartnerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereShareType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereShares($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Partner withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Partner extends Model
{
    /** @use HasFactory<PartnerFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'shares' => 'float',
        'share_type' => PartnerShareTypeEnum::class,
    ];

    protected static function booted(): void
    {
        static::created(function (Partner $model): void {
            PartnerCacheHelper::flush();
        });
        static::updated(function (Partner $model): void {
            PartnerCacheHelper::flush();
        });
    }

    /**
     * @return Factory<Partner>
     */
    protected static function newFactory(): Factory
    {
        return PartnerFactory::new();
    }

    /**
     * @return BelongsTo<Business, $this>
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function getShares(float $amount): float
    {
        if ($this->share_type === PartnerShareTypeEnum::Fixed) {
            return $this->shares;
        }

        return round($amount * ($this->shares / 100), 2);
    }
}
