<?php

declare(strict_types=1);

namespace App\Features\BalanceTransaction\Models;

use App\Features\Business\Models\Business;
use Bavix\Wallet\Models\Transaction;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $payable_type
 * @property string $payable_id
 * @property int $wallet_id
 * @property string $type
 * @property string $amount
 * @property bool $confirmed
 * @property array<array-key, mixed>|null $meta
 * @property string $uuid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Business|null $business
 * @property string $amount_float
 * @property-read int $amount_int
 * @property-read \Illuminate\Database\Eloquent\Model $payable
 * @property-read \App\Features\Wallet\Models\Wallet $wallet
 *
 * @method static Builder<static>|BalanceTransaction newModelQuery()
 * @method static Builder<static>|BalanceTransaction newQuery()
 * @method static Builder<static>|BalanceTransaction onlyTrashed()
 * @method static Builder<static>|BalanceTransaction query()
 * @method static Builder<static>|BalanceTransaction whereAmount($value)
 * @method static Builder<static>|BalanceTransaction whereConfirmed($value)
 * @method static Builder<static>|BalanceTransaction whereCreatedAt($value)
 * @method static Builder<static>|BalanceTransaction whereDeletedAt($value)
 * @method static Builder<static>|BalanceTransaction whereId($value)
 * @method static Builder<static>|BalanceTransaction whereMeta($value)
 * @method static Builder<static>|BalanceTransaction wherePayableId($value)
 * @method static Builder<static>|BalanceTransaction wherePayableType($value)
 * @method static Builder<static>|BalanceTransaction whereType($value)
 * @method static Builder<static>|BalanceTransaction whereUpdatedAt($value)
 * @method static Builder<static>|BalanceTransaction whereUuid($value)
 * @method static Builder<static>|BalanceTransaction whereWalletId($value)
 * @method static Builder<static>|BalanceTransaction withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|BalanceTransaction withoutTrashed()
 *
 * @mixin \Eloquent
 */
class BalanceTransaction extends Transaction
{
    protected static function booted(): void
    {
        static::addGlobalScope('only_business', function (Builder $builder): void {
            /** @var ?Business $business */
            $business = Filament::getTenant();
            if ($business === null) {
                return;
            }

            $builder->where('payable_id', $business->id)
                ->where('payable_type', Business::class);
        });
    }

    /**
     * @return BelongsTo<Business, $this>
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'payable_id');
    }
}
