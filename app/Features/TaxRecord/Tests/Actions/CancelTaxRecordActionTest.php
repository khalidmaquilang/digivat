<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Actions;

use App\Features\TaxRecord\Actions\CancelTaxRecordAction;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CancelTaxRecordActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_cancel_tax_record(): void
    {
        $user = User::factory()->create();
        $taxRecord = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);

        $result = app(CancelTaxRecordAction::class)->handle($taxRecord);

        $this->assertInstanceOf(TaxRecord::class, $result);
        $this->assertEquals(TaxRecordStatusEnum::Cancelled, $result->status);
        $this->assertDatabaseHas('tax_records', [
            'id' => $taxRecord->id,
            'status' => TaxRecordStatusEnum::Cancelled->value,
        ]);
    }

    public function test_does_not_update_already_cancelled_tax_record(): void
    {
        $user = User::factory()->create();
        $taxRecord = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Cancelled,
        ]);

        $originalUpdatedAt = $taxRecord->updated_at;

        $result = app(CancelTaxRecordAction::class)->handle($taxRecord);

        $this->assertInstanceOf(TaxRecord::class, $result);
        $this->assertEquals(TaxRecordStatusEnum::Cancelled, $result->status);
        $this->assertEquals($originalUpdatedAt, $result->updated_at);
    }
}
