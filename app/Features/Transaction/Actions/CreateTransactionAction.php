<?php

declare(strict_types=1);

namespace App\Features\Transaction\Actions;

use App\Features\Business\Models\Business;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Transaction\Enums\TransactionStatusEnum;
use App\Features\Transaction\Enums\TransactionTypeEnum;
use App\Features\Transaction\Models\Transaction;

class CreateTransactionAction
{
    /**
     * @param  array<array-key, mixed>  $metadata
     */
    public function handle(
        TaxRecord $tax_record,
        Business $business,
        TransactionTypeEnum $type = TransactionTypeEnum::TaxRemittance,
        ?string $reference_number = null,
        ?string $description = null,
        array $metadata = []
    ): Transaction {
        return Transaction::create([
            'tax_record_id' => $tax_record->id,
            'business_id' => $business->id,
            'amount' => $tax_record->tax_amount,
            'reference_number' => $reference_number ?? Transaction::generateReferenceNumber(),
            'type' => $type,
            'description' => $description ?? 'Tax remittance for tax record '.$tax_record->id,
            'transaction_date' => now(),
            'status' => TransactionStatusEnum::Completed,
            'metadata' => array_merge([
                'tax_record_reference' => $tax_record->transaction_reference,
                'gross_amount' => $tax_record->gross_amount,
                'taxable_amount' => $tax_record->taxable_amount,
                'tax_amount' => $tax_record->tax_amount,
                'total_amount' => $tax_record->total_amount,
            ], $metadata),
        ]);
    }
}
