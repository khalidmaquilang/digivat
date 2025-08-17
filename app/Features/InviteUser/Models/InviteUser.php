<?php

declare(strict_types=1);

namespace App\Features\InviteUser\Models;

use App\Features\Business\Models\Traits\HasBusinessTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $business_id
 * @property string $email
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Features\Business\Models\Business $business
 *
 * @method static \Database\Factories\InviteUserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InviteUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InviteUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InviteUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InviteUser whereBusinessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InviteUser whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InviteUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InviteUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InviteUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InviteUser whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class InviteUser extends Model
{
    use HasBusinessTrait;
    use HasFactory;
    use HasUuids;

    protected static function newFactory(): \Database\Factories\InviteUserFactory
    {
        return \Database\Factories\InviteUserFactory::new();
    }
}
