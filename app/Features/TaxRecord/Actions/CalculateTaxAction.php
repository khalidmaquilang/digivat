<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Actions;

use App\Features\TaxRecord\Data\CalculatedTaxRecordData;
use App\Features\TaxRecord\Data\CalculateTaxRecordData;
use App\Features\TaxRecord\Enums\CalculateTaxRecordModeEnum;
use App\Features\TaxRecordItem\Actions\CalculateTaxRecordItemAction;
use App\Features\TaxRecordItem\Actions\CreateTaxRecordItemAction;
use Exception;
use Illuminate\Support\Facades\DB;
use Log;

class CalculateTaxAction
{
    public function __construct(
        protected CalculateTaxRecordItemAction $calculate_record_item_action,
        protected CreateTaxRecordAction $create_tax_record_action,
        protected CreateTaxRecordItemAction $create_tax_record_item_action,
        protected GetTotalGrossAmountAction $get_total_gross_amount_action,
        protected GetTaxByCategoryAction $get_tax_by_category_action
    ) {}

    /**
     * @return array<string, mixed>
     *
     * @throws \Throwable
     */
    public function handle(CalculateTaxRecordData $data, string $business_id, ?string $referer_url = null): array
    {
        // calculate total amount of the item
        $total_item_amount = $this->get_total_gross_amount_action->handle($data->items);

        $taxable_amount = $total_item_amount - $data->order_discount;

        $tax_amount = $this->get_tax_by_category_action->handle($data->category_type, $taxable_amount);

        $tax_record_data = $data->toTaxRecordData(
            business_id: $business_id,
            gross_amount: $total_item_amount,
            taxable_amount: $taxable_amount,
            tax_amount: $tax_amount
        );

        if ($data->mode === CalculateTaxRecordModeEnum::Preview) {
            return $data->toCalculatedTaxRecordData(
                gross_amount: $total_item_amount,
                taxable_amount: $taxable_amount,
                tax_amount: $tax_amount,
            )->toArray();
        }

        // Add referer
        $tax_record_data->referer = $referer_url;

        try {
            DB::beginTransaction();
            $tax_record = $this->create_tax_record_action->handle($tax_record_data);

            foreach ($data->items as $item) {
                $item->tax_record_id = $tax_record->id;
                $this->create_tax_record_item_action->handle($item);
            }

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage(), [
                'business_id' => $business_id,
                'data' => $data->toArray(),
                'exception' => $exception,
            ]);

            throw $exception;
        }

        return CalculatedTaxRecordData::from($tax_record)->toArray();
    }
}
