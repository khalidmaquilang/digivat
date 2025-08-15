<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Models;

use App\Features\Shared\Models\Casts\Money;
use App\Features\Shared\Models\Scopes\UserScope;
use App\Features\Shared\Models\Traits\HasUuidsTrait;
use App\Features\TaxRecord\Database\Factories\TaxRecordFactory;
use App\Features\TaxRecord\Enums\CategoryTypeEnum;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecordItem\Models\TaxRecordItem;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $sales_date
 * @property string $transaction_reference
 * @property \Brick\Money\Money|float $gross_amount
 * @property \Brick\Money\Money|float $order_discount
 * @property \Brick\Money\Money|float $taxable_amount
 * @property \Brick\Money\Money|float $tax_amount
 * @property \Brick\Money\Money|float $total_amount
 * @property \Illuminate\Support\Carbon $valid_until
 * @property TaxRecordStatusEnum $status
 * @property CategoryTypeEnum $category_type
 * @property string|null $bir_receipt_id
 * @property string|null $referer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TaxRecordItem> $taxRecordItems
 * @property-read int|null $tax_record_items_count
 *
 * @method static \App\Features\TaxRecord\Database\Factories\TaxRecordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereBirReceiptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereCategoryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereGrossAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereOrderDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecord whereReferer($value)
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
#[ScopedBy(UserScope::class)]
class TaxRecord extends Model
{
    /** @use HasFactory<TaxRecordFactory> */
    use HasFactory;

    use HasUuidsTrait;
    use SoftDeletes;

    protected static function newFactory(): TaxRecordFactory
    {
        // Explicitly point to the correct factory class
        return TaxRecordFactory::new();
    }

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
