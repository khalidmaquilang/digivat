<?php

declare(strict_types=1);

namespace App\Features\TaxRecordItem\Database\Factories;

use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\TaxRecordItem\Models\TaxRecordItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaxRecordItem>
 */
final class TaxRecordItemFactory extends Factory
{
    protected $model = TaxRecordItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(2, 10, 1000);
        $quantity = fake()->numberBetween(1, 10);
        $discountAmount = fake()->randomFloat(2, 0, min($unitPrice * $quantity * 0.2, 50));
        $total = ($unitPrice * $quantity) - $discountAmount;

        return [
            'tax_record_id' => TaxRecord::factory(),
            'item_name' => fake()->words(3, true),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'discount_amount' => $discountAmount,
            'total' => $total,
        ];
    }

    /**
     * Set specific unit price.
     */
    public function withUnitPrice(float $unitPrice): static
    {
        return $this->state(fn (array $attributes): array => [
            'unit_price' => $unitPrice,
            'total' => ($unitPrice * $attributes['quantity']) - $attributes['discount_amount'],
        ]);
    }

    /**
     * Set specific quantity.
     */
    public function withQuantity(int $quantity): static
    {
        return $this->state(fn (array $attributes): array => [
            'quantity' => $quantity,
            'total' => ($attributes['unit_price'] * $quantity) - $attributes['discount_amount'],
        ]);
    }

    /**
     * Set specific discount amount.
     */
    public function withDiscount(float $discountAmount): static
    {
        return $this->state(fn (array $attributes): array => [
            'discount_amount' => $discountAmount,
            'total' => ($attributes['unit_price'] * $attributes['quantity']) - $discountAmount,
        ]);
    }

    /**
     * Set no discount.
     */
    public function withoutDiscount(): static
    {
        return $this->state(fn (array $attributes): array => [
            'discount_amount' => 0.0,
            'total' => $attributes['unit_price'] * $attributes['quantity'],
        ]);
    }

    /**
     * Set consistent amounts for testing.
     */
    public function withConsistentAmounts(float $unitPrice = 100.0, int $quantity = 1, float $discountAmount = 0.0): static
    {
        $total = ($unitPrice * $quantity) - $discountAmount;

        return $this->state(fn (array $attributes): array => [
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'discount_amount' => $discountAmount,
            'total' => $total,
        ]);
    }
}
