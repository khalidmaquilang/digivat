<?php

declare(strict_types=1);

namespace App\Features\Token\Models;

use App\Features\Business\Models\Business;
use App\Features\Business\Models\Traits\HasBusinessTrait;
use App\Features\Token\Database\Factories\TokenFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $business_id
 * @property string $name
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Business $business
 *
 * @method static \App\Features\Token\Database\Factories\TokenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Token withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Token extends Model
{
    use HasBusinessTrait;

    /** @use HasFactory<TokenFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (Token $token): void {
            $token->token = Str::random(50);
        });
    }

    protected static function newFactory(): TokenFactory
    {
        // Explicitly point to the correct factory class
        return TokenFactory::new();
    }
}
