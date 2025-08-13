<?php

declare(strict_types=1);

namespace Features\TaxRecordItem\Models;

use Features\Shared\Models\Casts\Money;
use Features\TaxRecordItem\Database\Factories\TaxRecordItemFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $tax_record_id
 * @property string $item_name
 * @property int $quantity
 * @property \Brick\Money\Money|float $unit_price
 * @property \Brick\Money\Money|float $discount_amount
 * @property \Brick\Money\Money|float $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static \Features\TaxRecordItem\Database\Factories\TaxRecordItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem whereItemName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem whereTaxRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaxRecordItem withoutTrashed()
 *
 * @mixin \Eloquent
 */
class TaxRecordItem extends Model
{
    /** @use HasFactory<TaxRecordItemFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected static function newFactory(): TaxRecordItemFactory
    {
        // Explicitly point to the correct factory class
        return TaxRecordItemFactory::new();
    }

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => Money::class,
        'discount_amount' => Money::class,
        'total' => Money::class,
    ];
}
