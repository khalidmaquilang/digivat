<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Actions;

use App\Features\Business\Models\Business;
use App\Features\TaxRecord\Actions\UpdateTaxRecordStatusAction;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UpdateTaxRecordStatusActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_tax_record_status(): void
    {
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'business_id' => $business->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);

        app(UpdateTaxRecordStatusAction::class)->handle(
            $tax_record,
            TaxRecordStatusEnum::Paid
        );

        $this->assertDatabaseHas('tax_records', [
            'id' => $tax_record->id,
            'status' => TaxRecordStatusEnum::Paid->value,
            'cancel_reason' => null,
        ]);
    }

    public function test_can_update_tax_record_status_with_cancel_reason(): void
    {
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'business_id' => $business->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);

        app(UpdateTaxRecordStatusAction::class)->handle(
            $tax_record,
            TaxRecordStatusEnum::Cancelled,
            'Customer requested cancellation'
        );

        $this->assertDatabaseHas('tax_records', [
            'id' => $tax_record->id,
            'status' => TaxRecordStatusEnum::Cancelled->value,
            'cancel_reason' => 'Customer requested cancellation',
        ]);
    }

    public function test_preserves_existing_cancel_reason_when_not_provided(): void
    {
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'business_id' => $business->id,
            'status' => TaxRecordStatusEnum::Cancelled,
            'cancel_reason' => 'Previously cancelled',
        ]);

        app(UpdateTaxRecordStatusAction::class)->handle(
            $tax_record,
            TaxRecordStatusEnum::Paid
        );

        $this->assertDatabaseHas('tax_records', [
            'id' => $tax_record->id,
            'status' => TaxRecordStatusEnum::Paid->value,
            'cancel_reason' => 'Previously cancelled',
        ]);
    }

    public function test_can_update_from_acknowledged_to_cancelled(): void
    {
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'business_id' => $business->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);

        app(UpdateTaxRecordStatusAction::class)->handle(
            $tax_record,
            TaxRecordStatusEnum::Cancelled,
            'System error'
        );

        $tax_record->refresh();
        $this->assertEquals(TaxRecordStatusEnum::Cancelled, $tax_record->status);
        $this->assertEquals('System error', $tax_record->cancel_reason);
    }

    public function test_can_update_from_paid_to_cancelled(): void
    {
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'business_id' => $business->id,
            'status' => TaxRecordStatusEnum::Paid,
        ]);

        app(UpdateTaxRecordStatusAction::class)->handle(
            $tax_record,
            TaxRecordStatusEnum::Cancelled,
            'Payment refund requested'
        );

        $tax_record->refresh();
        $this->assertEquals(TaxRecordStatusEnum::Cancelled, $tax_record->status);
        $this->assertEquals('Payment refund requested', $tax_record->cancel_reason);
    }

    public function test_can_update_from_cancelled_to_paid(): void
    {
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'business_id' => $business->id,
            'status' => TaxRecordStatusEnum::Cancelled,
            'cancel_reason' => 'Previously cancelled',
        ]);

        app(UpdateTaxRecordStatusAction::class)->handle(
            $tax_record,
            TaxRecordStatusEnum::Paid
        );

        $tax_record->refresh();
        $this->assertEquals(TaxRecordStatusEnum::Paid, $tax_record->status);
        $this->assertEquals('Previously cancelled', $tax_record->cancel_reason);
    }

    public function test_overwrites_cancel_reason_when_provided(): void
    {
        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'business_id' => $business->id,
            'status' => TaxRecordStatusEnum::Cancelled,
            'cancel_reason' => 'Old reason',
        ]);

        app(UpdateTaxRecordStatusAction::class)->handle(
            $tax_record,
            TaxRecordStatusEnum::Cancelled,
            'New cancellation reason'
        );

        $tax_record->refresh();
        $this->assertEquals(TaxRecordStatusEnum::Cancelled, $tax_record->status);
        $this->assertEquals('New cancellation reason', $tax_record->cancel_reason);
    }
}
