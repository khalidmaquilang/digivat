<?php

declare(strict_types=1);

namespace Features\TaxRecord\Models;

use Features\Shared\Models\Casts\Money;
use Features\Shared\Models\Traits\HasUuidsTrait;
use Features\TaxRecord\Enums\CategoryTypeEnum;
use Features\TaxRecord\Enums\TaxRecordStatusEnum;
use Features\TaxRecordItem\Models\TaxRecordItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $sales_date
 * @property string $transaction_reference
 * @property \Brick\Money\Money|float $order_discount
 * @property \Brick\Money\Money|float $gross_amount
 * @property int $discount_amount
 * @property \Brick\Money\Money|float $taxable_amount
 * @property \Brick\Money\Money|float $tax_amount
 * @property \Brick\Money\Money|float $total_amount
 * @property \Illuminate\Support\Carbon $valid_until
 * @property TaxRecordStatusEnum $status
 * @property CategoryTypeEnum $category_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TaxRecordItem> $taxRecordItems
 * @property-read int|null $tax_record_items_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereCategoryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereGrossAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereOrderDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereSalesDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereTaxableAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereTransactionReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereValidUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord withoutTrashed()
 *
 * @mixin \Eloquent
 */
class TaxRecord extends Model
{
    use HasUuidsTrait;
    use SoftDeletes;

    protected function getPrefixId(): string
    {
        return 'BIR-TX-';
    }

    protected function isValidUniqueId(mixed $value): bool
    {
        return true;
    }

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'gross_amount' => Money::class,
        'order_discount' => Money::class,
        'taxable_amount' => Money::class,
        'tax_amount' => Money::class,
        'total_amount' => Money::class,
        'sales_date' => 'datetime',
        'valid_until' => 'date',
        'status' => TaxRecordStatusEnum::class,
        'category_type' => CategoryTypeEnum::class,
    ];

    /**
     * @return HasMany<TaxRecordItem, $this>
     */
    public function taxRecordItems(): HasMany
    {
        return $this->hasMany(TaxRecordItem::class);
    }
}
