<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Actions;

use App\Features\TaxRecord\Actions\CreateTaxRecordAction;
use App\Features\TaxRecord\Data\TaxRecordData;
use App\Features\TaxRecord\Enums\CategoryTypeEnum;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateTaxRecordActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_tax_record(): void
    {
        $user = User::factory()->create();

        $data = new TaxRecordData(
            user_id: $user->id,
            sales_date: now(),
            transaction_reference: 'reference',
            gross_amount: 110,
            order_discount: 10,
            taxable_amount: 100,
            tax_amount: 12,
            status: TaxRecordStatusEnum::Acknowledged,
            category_type: CategoryTypeEnum::DIGITAL_STREAMING,
            valid_until: now()->addMonth()
        );

        app(CreateTaxRecordAction::class)->handle($data);

        $this->assertDatabaseHas('tax_records', [
            'user_id' => $data->user_id,
            'sales_date' => $data->sales_date,
            'transaction_reference' => $data->transaction_reference,
            'gross_amount' => $this->convertMoney($data->gross_amount),
            'order_discount' => $this->convertMoney($data->order_discount),
            'taxable_amount' => $this->convertMoney($data->taxable_amount),
            'tax_amount' => $this->convertMoney($data->tax_amount),
            'total_amount' => $this->convertMoney($data->total_amount),
            'valid_until' => $data->valid_until,
            'status' => $data->status,
            'category_type' => $data->category_type,
        ]);
    }
}
