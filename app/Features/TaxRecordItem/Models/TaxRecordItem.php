<?php

declare(strict_types=1);

namespace Features\TaxRecordItem\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $tax_record_id
 * @property string $item_name
 * @property int $quantity
 * @property int $unit_price
 * @property int $discount_amount
 * @property int $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
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
    use HasUuids;
    use SoftDeletes;
}
