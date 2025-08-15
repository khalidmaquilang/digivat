<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Data;

use App\Features\TaxRecord\Enums\CalculateTaxRecordModeEnum;
use App\Features\TaxRecord\Enums\CategoryTypeEnum;
use App\Features\TaxRecordItem\Data\TaxRecordItemData;
use Carbon\Carbon;
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
