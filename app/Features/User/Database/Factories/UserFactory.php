<?php

declare(strict_types=1);

namespace App\Features\User\Database\Factories;

use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Features\User\Models\User>
 */
final class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName,
            'middle_name' => fake()->word,
            'last_name' => fake()->lastName,
            'suffix' => fake()->optional()->word,
            'nickname' => fake()->optional()->word,
            'email' => fake()->safeEmail,
            'tin_number' => fake()->optional()->word,
            'email_verified_at' => fake()->optional()->dateTime(),
            'password' => bcrypt(fake()->password),
            'remember_token' => Str::random(10),
        ];
    }
}
