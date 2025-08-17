<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\\Features\\InviteUser\\Models\\InviteUser>
 */
class InviteUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = \App\Features\InviteUser\Models\InviteUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => \App\Features\Business\Models\Business::factory(),
            'email' => fake()->unique()->safeEmail(),
            'code' => fake()->unique()->regexify('[A-Z0-9]{8}'),
        ];
    }
}
