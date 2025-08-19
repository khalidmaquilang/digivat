<?php

declare(strict_types=1);

namespace App\Features\Wallet\Models;

/**
 * @property int $id
 * @property string $holder_type
 * @property string $holder_id
 * @property string $name
 * @property string $slug
 * @property string $uuid
 * @property string|null $description
 * @property array<array-key, mixed>|null $meta
 * @property non-empty-string $balance
 * @property int $decimal_places
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string|int|float $available_balance
 * @property-read non-empty-string $balance_float
 * @property-read float $balance_float_num
 * @property-read int $balance_int
 * @property-read string $credit
 * @property-read string $currency
 * @property-read string $original_balance
 * @property-read \Bavix\Wallet\Models\WalletModel|null $wallet
 * @property-read \Illuminate\Database\Eloquent\Model $holder
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Bavix\Wallet\Models\Transfer> $receivedTransfers
 * @property-read int|null $received_transfers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Bavix\Wallet\Models\Transfer> $transfers
 * @property-read int|null $transfers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Features\BalanceTransaction\Models\BalanceTransaction> $walletTransactions
 * @property-read int|null $wallet_transactions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereDecimalPlaces($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereHolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereHolderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Wallet withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Wallet extends \Bavix\Wallet\Models\Wallet {}
