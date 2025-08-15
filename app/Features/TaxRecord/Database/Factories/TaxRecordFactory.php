<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Database\Factories;

use App\Features\TaxRecord\Enums\CategoryTypeEnum;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Features\TaxRecord\Models\TaxRecord>
 */
final class TaxRecordFactory extends Factory
{
    protected $model = TaxRecord::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'sales_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'transaction_reference' => fake()->unique()->regexify('TX[0-9]{8}'),
            'gross_amount' => fake()->randomFloat(2, 100, 10000),
            'order_discount' => fake()->randomFloat(2, 0, 100),
            'taxable_amount' => fake()->randomFloat(2, 50, 9500),
            'tax_amount' => fake()->randomFloat(2, 5, 1500),
            'total_amount' => fake()->randomFloat(2, 55, 11000),
            'valid_until' => fake()->dateTimeBetween('+1 day', '+3 months'),
            'status' => fake()->randomElement(TaxRecordStatusEnum::cases()),
            'category_type' => fake()->randomElement(CategoryTypeEnum::cases()),
        ];
    }

    /**
     * Indicate that the tax record is acknowledged.
     */
    public function acknowledged(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);
    }

    /**
     * Indicate that the tax record is for digital streaming.
     */
    public function digitalStreaming(): static
    {
        return $this->state(fn (array $attributes): array => [
            'category_type' => CategoryTypeEnum::DIGITAL_STREAMING,
        ]);
    }

    /**
     * Set consistent amounts based on taxable amount.
     */
    public function withConsistentAmounts(float $taxableAmount = 100.0, float $taxRate = 0.12): static
    {
        $orderDiscount = fake()->randomFloat(2, 0, 20);
        $grossAmount = $taxableAmount + $orderDiscount;
        $taxAmount = $taxableAmount * $taxRate;
        $totalAmount = $taxableAmount + $taxAmount;

        return $this->state(fn (array $attributes): array => [
            'gross_amount' => $grossAmount,
            'order_discount' => $orderDiscount,
            'taxable_amount' => $taxableAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);
    }
}
