<?php

declare(strict_types=1);

namespace App\Features\TaxRecordItem\Data;

use Spatie\LaravelData\Attributes\Validation\GreaterThanOrEqualTo;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Normalizers\ArrayNormalizer;
use Spatie\LaravelData\Normalizers\JsonNormalizer;
use Spatie\LaravelData\Normalizers\ObjectNormalizer;

class TaxRecordItemData extends Data
{
    public function __construct(
        #[GreaterThanOrEqualTo(0)]
        public float $unit_price,
        #[Required]
        public ?string $item_name,
        public ?string $tax_record_id = null,
        #[GreaterThanOrEqualTo(1)]
        public int $quantity = 1,
        #[GreaterThanOrEqualTo(0)]
        public float $discount_amount = 0,
        public float $total = 0
    ) {
        $this->total = ($this->unit_price * $this->quantity) - $this->discount_amount;
    }

    public static function normalizers(): array
    {
        return [
            ObjectNormalizer::class,
            ArrayNormalizer::class,
            JsonNormalizer::class,
        ];
    }
}
