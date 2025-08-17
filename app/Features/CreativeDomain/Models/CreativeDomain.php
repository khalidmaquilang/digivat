<?php

declare(strict_types=1);

namespace App\Features\CreativeDomain\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreativeDomain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreativeDomain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreativeDomain onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreativeDomain query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreativeDomain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreativeDomain whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreativeDomain whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreativeDomain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreativeDomain whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreativeDomain whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreativeDomain withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreativeDomain withoutTrashed()
 *
 * @mixin \Eloquent
 */
class CreativeDomain extends Model
{
    use HasUuids;
    //
    use SoftDeletes;
}
