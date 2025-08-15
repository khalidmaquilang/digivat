<?php

declare(strict_types=1);

namespace Features\TaxRecord\Data;

use Carbon\Carbon;
use Features\Shared\Helpers\MoneyHelper;
use Features\TaxRecord\Enums\CalculateTaxRecordModeEnum;
use Features\TaxRecord\Enums\CategoryTypeEnum;
use Features\TaxRecord\Enums\TaxRecordStatusEnum;
use Features\TaxRecordItem\Data\TaxRecordItemData;
use Spatie\LaravelData\Attributes\Validation\GreaterThanOrEqualTo;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CalculateTaxRecordData extends Data
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

    public function toTaxRecordData(
        string $user_id,
        float $gross_amount,
        float $taxable_amount,
        float $tax_amount,
        Carbon $valid_until,
    ): TaxRecordData {
        return new TaxRecordData(
            user_id: $user_id,
            sales_date: $this->sales_date,
            transaction_reference: $this->transaction_reference,
            gross_amount: $gross_amount,
            order_discount: $this->order_discount,
            taxable_amount: $taxable_amount,
            tax_amount: MoneyHelper::evaluate($tax_amount),
            status: $this->mode === CalculateTaxRecordModeEnum::Acknowledge ? TaxRecordStatusEnum::Acknowledged : TaxRecordStatusEnum::Preview,
            category_type: $this->category_type,
            bir_receipt_id: Optional::create(),
            valid_until: $valid_until,
        );
    }
}
