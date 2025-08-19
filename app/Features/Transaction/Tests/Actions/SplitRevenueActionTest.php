<?php

declare(strict_types=1);

namespace App\Features\Transaction\Tests\Actions;

use App\Features\BalanceTransaction\Actions\DepositAction;
use App\Features\Business\Models\Business;
use App\Features\Partner\Actions\GetAvailablePartnersAction;
use App\Features\Partner\Enums\PartnerShareTypeEnum;
use App\Features\Partner\Helpers\PartnerCacheHelper;
use App\Features\Partner\Models\Partner;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Transaction\Actions\SplitRevenueAction;
use App\Features\Transaction\Enums\TransactionStatusEnum;
use App\Features\Transaction\Enums\TransactionTypeEnum;
use App\Features\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SplitRevenueActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_split_revenue_among_partners_with_percentage_shares(): void
    {
        // Create businesses
        $mainBusiness = Business::factory()->create();
        $partnerBusiness1 = Business::factory()->create();
        $partnerBusiness2 = Business::factory()->create();

        // Create partners with percentage shares
        Partner::create([
            'id' => fake()->uuid(),
            'business_id' => $partnerBusiness1->id,
            'shares' => 30.0, // 30%
            'share_type' => PartnerShareTypeEnum::Percentage,
        ]);

        Partner::create([
            'id' => fake()->uuid(),
            'business_id' => $partnerBusiness2->id,
            'shares' => 20.0, // 20%
            'share_type' => PartnerShareTypeEnum::Percentage,
        ]);

        // Create transaction
        $taxRecord = TaxRecord::factory()->for($mainBusiness)->create();
        $transaction = Transaction::create([
            'id' => 'TXN-'.fake()->uuid(),
            'business_id' => $mainBusiness->id,
            'tax_record_id' => $taxRecord->id,
            'amount' => 1000.00,
            'reference_number' => Transaction::generateReferenceNumber(),
            'type' => TransactionTypeEnum::TaxRemittance,
            'transaction_date' => now(),
            'status' => TransactionStatusEnum::Completed,
        ]);

        $partner1InitialBalance = $partnerBusiness1->balanceFloat;
        $partner2InitialBalance = $partnerBusiness2->balanceFloat;

        app(SplitRevenueAction::class)->handle($transaction);

        $partnerBusiness1->refresh();
        $partnerBusiness2->refresh();

        // Assert partner 1 received 30% (300.00)
        $this->assertEquals($partner1InitialBalance + 300.00, $partnerBusiness1->balanceFloat);

        // Assert partner 2 received 20% (200.00)
        $this->assertEquals($partner2InitialBalance + 200.00, $partnerBusiness2->balanceFloat);
    }

    public function test_can_split_revenue_among_partners_with_fixed_shares(): void
    {
        $mainBusiness = Business::factory()->create();
        $partnerBusiness1 = Business::factory()->create();
        $partnerBusiness2 = Business::factory()->create();

        // Create partners with fixed shares
        Partner::create([
            'id' => fake()->uuid(),
            'business_id' => $partnerBusiness1->id,
            'shares' => 150.0, // Fixed 150.00
            'share_type' => PartnerShareTypeEnum::Fixed,
        ]);

        Partner::create([
            'id' => fake()->uuid(),
            'business_id' => $partnerBusiness2->id,
            'shares' => 75.0, // Fixed 75.00
            'share_type' => PartnerShareTypeEnum::Fixed,
        ]);

        $taxRecord = TaxRecord::factory()->for($mainBusiness)->create();
        $transaction = Transaction::create([
            'id' => 'TXN-'.fake()->uuid(),
            'business_id' => $mainBusiness->id,
            'tax_record_id' => $taxRecord->id,
            'amount' => 1000.00,
            'reference_number' => Transaction::generateReferenceNumber(),
            'type' => TransactionTypeEnum::TaxRemittance,
            'transaction_date' => now(),
            'status' => TransactionStatusEnum::Completed,
        ]);

        $partner1InitialBalance = $partnerBusiness1->balanceFloat;
        $partner2InitialBalance = $partnerBusiness2->balanceFloat;

        app(SplitRevenueAction::class)->handle($transaction);

        $partnerBusiness1->refresh();
        $partnerBusiness2->refresh();

        // Assert partner 1 received fixed amount (150.00)
        $this->assertEquals($partner1InitialBalance + 150.00, $partnerBusiness1->balanceFloat);

        // Assert partner 2 received fixed amount (75.00)
        $this->assertEquals($partner2InitialBalance + 75.00, $partnerBusiness2->balanceFloat);
    }

    public function test_does_nothing_when_no_partners_available(): void
    {
        $mainBusiness = Business::factory()->create();

        $taxRecord = TaxRecord::factory()->for($mainBusiness)->create();
        $transaction = Transaction::create([
            'id' => 'TXN-'.fake()->uuid(),
            'business_id' => $mainBusiness->id,
            'tax_record_id' => $taxRecord->id,
            'amount' => 1000.00,
            'reference_number' => Transaction::generateReferenceNumber(),
            'type' => TransactionTypeEnum::TaxRemittance,
            'transaction_date' => now(),
            'status' => TransactionStatusEnum::Completed,
        ]);

        // Ensure no partners exist
        Partner::query()->delete();
        PartnerCacheHelper::flush();

        // This should not throw any errors
        app(SplitRevenueAction::class)->handle($transaction);

        // No assertions needed as we're testing it doesn't crash
        $this->assertTrue(true);
    }

    public function test_uses_dependency_injection_correctly(): void
    {
        $getAvailablePartnersAction = $this->createMock(GetAvailablePartnersAction::class);
        $depositAction = $this->createMock(DepositAction::class);

        $getAvailablePartnersAction
            ->expects($this->once())
            ->method('handle')
            ->willReturn(new Collection);

        $depositAction
            ->expects($this->never())
            ->method('handle');

        $splitRevenueAction = new SplitRevenueAction(
            $getAvailablePartnersAction,
            $depositAction
        );

        $business = Business::factory()->create();
        $taxRecord = TaxRecord::factory()->for($business)->create();
        $transaction = Transaction::create([
            'id' => 'TXN-'.fake()->uuid(),
            'business_id' => $business->id,
            'tax_record_id' => $taxRecord->id,
            'amount' => 1000.00,
            'reference_number' => Transaction::generateReferenceNumber(),
            'type' => TransactionTypeEnum::TaxRemittance,
            'transaction_date' => now(),
            'status' => TransactionStatusEnum::Completed,
        ]);

        $splitRevenueAction->handle($transaction);
    }

    public function test_handles_mixed_share_types(): void
    {
        $mainBusiness = Business::factory()->create();
        $partnerBusiness1 = Business::factory()->create();
        $partnerBusiness2 = Business::factory()->create();

        // Mix of percentage and fixed shares
        Partner::create([
            'id' => fake()->uuid(),
            'business_id' => $partnerBusiness1->id,
            'shares' => 25.0, // 25%
            'share_type' => PartnerShareTypeEnum::Percentage,
        ]);

        Partner::create([
            'id' => fake()->uuid(),
            'business_id' => $partnerBusiness2->id,
            'shares' => 100.0, // Fixed 100.00
            'share_type' => PartnerShareTypeEnum::Fixed,
        ]);

        $taxRecord = TaxRecord::factory()->for($mainBusiness)->create();
        $transaction = Transaction::create([
            'id' => 'TXN-'.fake()->uuid(),
            'business_id' => $mainBusiness->id,
            'tax_record_id' => $taxRecord->id,
            'amount' => 800.00,
            'reference_number' => Transaction::generateReferenceNumber(),
            'type' => TransactionTypeEnum::TaxRemittance,
            'transaction_date' => now(),
            'status' => TransactionStatusEnum::Completed,
        ]);

        $partner1InitialBalance = $partnerBusiness1->balanceFloat;
        $partner2InitialBalance = $partnerBusiness2->balanceFloat;

        app(SplitRevenueAction::class)->handle($transaction);

        $partnerBusiness1->refresh();
        $partnerBusiness2->refresh();

        // Partner 1: 25% of 800.00 = 200.00
        $this->assertEquals($partner1InitialBalance + 200.00, $partnerBusiness1->balanceFloat);

        // Partner 2: Fixed 100.00
        $this->assertEquals($partner2InitialBalance + 100.00, $partnerBusiness2->balanceFloat);
    }
}
