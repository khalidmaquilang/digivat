<?php

declare(strict_types=1);

namespace App\Features\TaxRecordItem\Data;

use Spatie\LaravelData\Attributes\Validation\GreaterThanOrEqualTo;
use Spatie\LaravelData\Data;

class TaxRecordItemData extends Data
{
    public function __construct(
        public string $item_name,
        #[GreaterThanOrEqualTo(0)]
        public float $unit_price,
        public ?string $tax_record_id = null,
        #[GreaterThanOrEqualTo(1)]
        public int $quantity = 1,
        #[GreaterThanOrEqualTo(0)]
        public float $discount_amount = 0,
        public float $total = 0
    ) {
        $this->total = ($this->unit_price * $this->quantity) - $this->discount_amount;
    }
}
