<?php

declare(strict_types=1);

namespace Features\TaxRecord\Data;

use Carbon\Carbon;
use Features\TaxRecord\Enums\CalculateTaxRecordModeEnum;
use Features\TaxRecord\Enums\CategoryTypeEnum;
use Features\TaxRecordItem\Data\TaxRecordItemData;
use Spatie\LaravelData\Attributes\Validation\GreaterThanOrEqualTo;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class CalculatedTaxRecordData extends Data
{
    public function __construct(
        public CalculateTaxRecordModeEnum $mode,
        public CategoryTypeEnum $category_type,
        public string $transaction_reference,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.uP')]
        public Carbon $sales_date,
        /**
         * @var array<TaxRecordItemData>
         */
        public array $items,
        #[GreaterThanOrEqualTo(0)]
        public float $order_discount = 0
    ) {}
}
