<?php

declare(strict_types=1);

namespace Features\TaxRecordItem\Data;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class TaxRecordItemData extends Data
{
    public function __construct(
        public string $item_name,
        #[Min(0)]
        public float $unit_price,
        public ?string $tax_record_id = null,
        #[Min(1)]
        public int $quantity = 1,
        #[Min(0)]
        public float $discount_amount = 0,
        public float $total = 0
    ) {
        $this->total = ($this->unit_price * $this->quantity) - $this->discount_amount;
    }
}
