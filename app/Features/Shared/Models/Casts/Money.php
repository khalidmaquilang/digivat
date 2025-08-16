<?php

declare(strict_types=1);

namespace App\Features\Shared\Models\Casts;

use Brick\Math\RoundingMode;
use Brick\Money\Money as BrickMoney;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<float, int>
 */
class Money implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): float
    {
        $raw = $attributes[$key] ?? 0;

        if (! is_numeric($raw)) {
            return 0;
        }

        return BrickMoney::ofMinor($raw, 'PHP', roundingMode: RoundingMode::DOWN)->getAmount()->toFloat();
    }

    /**
     * @param  int|string|null  $value
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        if (is_numeric($value)) {
            return BrickMoney::of($value, 'PHP', roundingMode: RoundingMode::DOWN)->getMinorAmount()->toInt();
        }

        return 0;
    }
}
