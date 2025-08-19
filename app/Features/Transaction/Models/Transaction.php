<?php

declare(strict_types=1);

namespace App\Features\Transaction\Models;

use App\Features\Business\Models\Traits\HasBusinessTrait;
use App\Features\Shared\Models\Casts\Money;
use App\Features\Shared\Models\Traits\HasUuidsTrait;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Transaction\Enums\TransactionStatusEnum;
use App\Features\Transaction\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $business_id
 * @property string $tax_record_id
 * @property float $amount
 * @property string $reference_number
 * @property TransactionTypeEnum $type
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property TransactionStatusEnum $status
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Features\Business\Models\Business $business
 * @property-read TaxRecord $taxRecord
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereReferenceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTaxRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    use HasBusinessTrait;
    use HasUuidsTrait;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'amount' => Money::class,
            'transaction_date' => 'datetime',
            'type' => TransactionTypeEnum::class,
            'status' => TransactionStatusEnum::class,
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<TaxRecord, $this>
     */
    public function taxRecord(): BelongsTo
    {
        return $this->belongsTo(TaxRecord::class);
    }

    protected function getPrefixId(): string
    {
        return 'TXN-';
    }

    protected function isValidUniqueId(mixed $value): bool
    {
        return true;
    }

    public static function generateReferenceNumber(): string
    {
        $timestamp = now()->format('YmdHis');
        $random = str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

        return sprintf('TXN-%s-%s', $timestamp, $random);
    }
}
