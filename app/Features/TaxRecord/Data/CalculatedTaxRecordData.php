<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class CalculatedTaxRecordData extends Data
{
    public function __construct(
        public string $transaction_reference,
        public float $gross_amount,
        public float $taxable_amount,
        public float $tax_amount,
        public float $total_amount = 0,
        public ?Carbon $valid_until = null,
        #[MapInputName('id')]
        public ?string $bir_receipt_id = null,
    ) {
        $this->total_amount = $taxable_amount + $tax_amount;
    }
}
