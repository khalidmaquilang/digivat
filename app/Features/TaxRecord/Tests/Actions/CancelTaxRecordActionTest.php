<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Actions;

use App\Features\Business\Models\Business;
use App\Features\TaxRecord\Actions\CancelTaxRecordAction;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CancelTaxRecordActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_cancel_tax_record(): void
    {
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'business_id' => $business->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);

        $result = app(CancelTaxRecordAction::class)->handle($tax_record);

        $this->assertInstanceOf(TaxRecord::class, $result);
        $this->assertEquals(TaxRecordStatusEnum::Cancelled, $result->status);
        $this->assertDatabaseHas('tax_records', [
            'id' => $tax_record->id,
            'status' => TaxRecordStatusEnum::Cancelled->value,
        ]);
    }

    public function test_does_not_update_already_cancelled_tax_record(): void
    {
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'business_id' => $business->id,
            'status' => TaxRecordStatusEnum::Cancelled,
        ]);

        $original_updated_at = $tax_record->updated_at;

        $result = app(CancelTaxRecordAction::class)->handle($tax_record);

        $this->assertInstanceOf(TaxRecord::class, $result);
        $this->assertEquals(TaxRecordStatusEnum::Cancelled, $result->status);
        $this->assertEquals($original_updated_at, $result->updated_at);
    }
}
