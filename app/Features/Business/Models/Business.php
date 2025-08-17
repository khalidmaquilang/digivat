<?php

declare(strict_types=1);

namespace App\Features\Business\Models;

use App\Features\Business\Database\Factories\BusinessFactory;
use App\Features\Business\Models\Traits\BusinessSchemaTrait;
use App\Features\CreativeDomain\Models\CreativeDomain;
use App\Features\InviteUser\Models\InviteUser;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Token\Models\Token;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $owner_id
 * @property string $name
 * @property string $slug
 * @property string|null $tin_number
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CreativeDomain> $creativeDomains
 * @property-read int|null $creative_domains_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, InviteUser> $inviteUsers
 * @property-read int|null $invite_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $members
 * @property-read int|null $members_count
 * @property-read User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TaxRecord> $taxRecords
 * @property-read int|null $tax_records_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Token> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static \App\Features\Business\Database\Factories\BusinessFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereTinNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Business withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Business extends Model
{
    use BusinessSchemaTrait;

    /** @use HasFactory<BusinessFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected static function newFactory(): BusinessFactory
    {
        return BusinessFactory::new();
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return HasMany<TaxRecord, $this>
     */
    public function taxRecords(): HasMany
    {
        return $this->hasMany(TaxRecord::class);
    }

    /**
     * @return HasMany<Token, $this>
     */
    public function tokens(): HasMany
    {
        return $this->hasMany(Token::class);
    }

    /**
     * @return HasMany<InviteUser, $this>
     */
    public function inviteUsers(): HasMany
    {
        return $this->hasMany(InviteUser::class);
    }

    /**
     * @return BelongsToMany<BelongsToMany, $this>
     */
    public function creativeDomains(): BelongsToMany
    {
        return $this->belongsToMany(CreativeDomain::class);
    }
}
