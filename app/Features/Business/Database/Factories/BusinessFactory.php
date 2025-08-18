<?php

declare(strict_types=1);

namespace App\Features\Business\Database\Factories;

use App\Features\Business\Models\Business;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Features\Business\Models\Business>
 */
final class BusinessFactory extends Factory
{
    protected $model = Business::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'owner_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->randomNumber(4),
            'tin_number' => fake()->optional()->numerify('###-###-###-###'),
            'logo' => fake()->optional()->imageUrl(),
        ];
    }
}
