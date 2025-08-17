<?php

declare(strict_types=1);

namespace App\Features\Token\Database\Factories;

use App\Features\Business\Models\Business;
use App\Features\Token\Models\Token;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Token>
 */
final class TokenFactory extends Factory
{
    protected $model = Token::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'name' => fake()->name,
            'token' => Str::random(10),
        ];
    }
}
