<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Actions;

use App\Features\TaxRecord\Actions\BulkCancelTaxRecordAction;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BulkCancelTaxRecordActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_bulk_cancel_tax_records(): void
    {
        $tax_records = TaxRecord::factory()->count(3)->create([
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);

        $result = app(BulkCancelTaxRecordAction::class)->handle($tax_records);

        $this->assertIsInt($result);
        $this->assertEquals(3, $result);

        // Verify all records are cancelled in database
        /** @var TaxRecord $tax_record */
        foreach ($tax_records as $tax_record) {
            $this->assertDatabaseHas('tax_records', [
                'id' => $tax_record->id,
                'status' => TaxRecordStatusEnum::Cancelled->value,
            ]);
        }
    }

    public function test_handles_mixed_status_tax_records(): void
    {
        $acknowledged_record = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);

        $cancelled_record = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Cancelled,
        ]);

        $paid_record = TaxRecord::factory()->create([
            'status' => TaxRecordStatusEnum::Paid,
        ]);

        $tax_records = new Collection([$acknowledged_record, $cancelled_record, $paid_record]);
        $result = app(BulkCancelTaxRecordAction::class)->handle($tax_records);

        $this->assertIsInt($result);
        $this->assertEquals(2, $result); // Only 2 records should be updated (acknowledged and paid)

        // Verify in database - all should now be cancelled
        $this->assertDatabaseHas('tax_records', [
            'id' => $acknowledged_record->id,
            'status' => TaxRecordStatusEnum::Cancelled->value,
        ]);

        $this->assertDatabaseHas('tax_records', [
            'id' => $cancelled_record->id,
            'status' => TaxRecordStatusEnum::Cancelled->value,
        ]);

        $this->assertDatabaseHas('tax_records', [
            'id' => $paid_record->id,
            'status' => TaxRecordStatusEnum::Cancelled->value,
        ]);
    }

    public function test_handles_empty_collection(): void
    {
        $empty_collection = new Collection;
        $result = app(BulkCancelTaxRecordAction::class)->handle($empty_collection);

        $this->assertIsInt($result);
        $this->assertEquals(0, $result);
    }
}
