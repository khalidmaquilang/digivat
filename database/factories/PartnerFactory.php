<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Features\Business\Models\Business;
use App\Features\Partner\Enums\PartnerShareTypeEnum;
use App\Features\Partner\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Partner>
 */
class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'shares' => $this->faker->randomFloat(2, 1, 100),
            'share_type' => $this->faker->randomElement(PartnerShareTypeEnum::cases()),
        ];
    }

    /**
     * Create a partner with percentage share type.
     */
    public function percentage(?float $percentage = null): static
    {
        return $this->state([
            'shares' => $percentage ?? $this->faker->randomFloat(2, 1, 100),
            'share_type' => PartnerShareTypeEnum::Percentage,
        ]);
    }

    /**
     * Create a partner with fixed share type.
     */
    public function fixed(?float $amount = null): static
    {
        return $this->state([
            'shares' => $amount ?? $this->faker->randomFloat(2, 10, 1000),
            'share_type' => PartnerShareTypeEnum::Fixed,
        ]);
    }
}
