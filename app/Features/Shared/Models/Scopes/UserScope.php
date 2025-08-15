<?php

declare(strict_types=1);

namespace App\Features\Shared\Models\Scopes;

use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UserScope implements Scope
{
    /**
     * @param  Builder<User>  $builder
     */
    public function apply(Builder $builder, Model $model): void
    {
        /** @var ?User $user */
        $user = auth()->user();

        if ($user === null || ! $user->id) {
            return;
        }

        $builder->where('user_id', $user->id);
    }
}
