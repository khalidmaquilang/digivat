<?php

declare(strict_types=1);

namespace Features\TaxRecord\Data;

use Carbon\Carbon;
use Features\TaxRecord\Enums\CategoryTypeEnum;
use Features\TaxRecord\Enums\TaxRecordStatusEnum;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class TaxRecordData extends Data
{
    public function __construct(
        public string $user_id,
        public Carbon $sales_date,
        public string $transaction_reference,
        public float $gross_amount,
        public float $order_discount,
        public float $taxable_amount,
        public float $tax_amount,
        public TaxRecordStatusEnum $status,
        public CategoryTypeEnum $category_type,
        public Optional|string|null $bir_receipt_id = null,
        public ?Carbon $valid_until = null,
        public float $total_amount = 0,
        public ?string $referer = null,
    ) {
        $this->total_amount = $taxable_amount + $tax_amount;
    }
}
