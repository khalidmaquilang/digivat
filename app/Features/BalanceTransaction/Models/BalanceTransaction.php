<?php

declare(strict_types=1);

namespace App\Features\BalanceTransaction\Models;

use Bavix\Wallet\Models\Transaction;

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
 * @property string $amount_float
 * @property-read int $amount_int
 * @property-read \Illuminate\Database\Eloquent\Model $payable
 * @property-read \App\Features\Wallet\Models\Wallet $wallet
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction wherePayableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction wherePayableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction whereWalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BalanceTransaction withoutTrashed()
 *
 * @mixin \Eloquent
 */
class BalanceTransaction extends Transaction {}
