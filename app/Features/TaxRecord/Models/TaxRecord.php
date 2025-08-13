<?php

declare(strict_types=1);

namespace Features\TaxRecord\Models;

use Features\Shared\Models\Traits\HasUuidsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $user_id
 * @property string|null $sales_date
 * @property string $transaction_reference
 * @property int $order_discount
 * @property int $gross_amount
 * @property int $discount_amount
 * @property int $taxable_amount
 * @property int $tax_amount
 * @property int $total_amount
 * @property string $valid_until
 * @property string $status
 * @property string $category_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
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
}
