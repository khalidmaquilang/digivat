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
        $main_business = Business::factory()->create();
        $partner_business_1 = Business::factory()->create();
        $partner_business_2 = Business::factory()->create();

        // Create partners with percentage shares
        Partner::create([
            'id' => fake()->uuid(),
            'business_id' => $partner_business_1->id,
            'shares' => 30.0, // 30%
            'share_type' => PartnerShareTypeEnum::Percentage,
        ]);

        Partner::create([
            'id' => fake()->uuid(),
            'business_id' => $partner_business_2->id,
            'shares' => 20.0, // 20%
            'share_type' => PartnerShareTypeEnum::Percentage,
        ]);

        // Create transaction
        $tax_record = TaxRecord::factory()->for($main_business)->create();
        $transaction = Transaction::create([
            'id' => 'TXN-'.fake()->uuid(),
            'business_id' => $main_business->id,
            'tax_record_id' => $tax_record->id,
            'amount' => 1000.00,
            'reference_number' => Transaction::generateReferenceNumber(),
            'type' => TransactionTypeEnum::TaxRemittance,
            'transaction_date' => now(),
            'status' => TransactionStatusEnum::Completed,
        ]);

        $partner_1_initial_balance = $partner_business_1->balanceFloatNum;
        $partner_2_initial_balance = $partner_business_2->balanceFloatNum;

        app(SplitRevenueAction::class)->handle($transaction);

        $partner_business_1->refresh();
        $partner_business_2->refresh();

        // Assert partner 1 received 30% (300.00)
        $this->assertEquals($partner_1_initial_balance + 300.00, $partner_business_1->balanceFloatNum);

        // Assert partner 2 received 20% (200.00)
        $this->assertEquals($partner_2_initial_balance + 200.00, $partner_business_2->balanceFloatNum);
    }

    public function test_can_split_revenue_among_partners_with_fixed_shares(): void
    {
        $main_business = Business::factory()->create();
        $partner_business_1 = Business::factory()->create();
        $partner_business_2 = Business::factory()->create();

        // Create partners with fixed shares
        Partner::create([
            'id' => fake()->uuid(),
            'business_id' => $partner_business_1->id,
            'shares' => 150.0, // Fixed 150.00
            'share_type' => PartnerShareTypeEnum::Fixed,
        ]);

        Partner::create([
            'id' => fake()->uuid(),
            'business_id' => $partner_business_2->id,
            'shares' => 75.0, // Fixed 75.00
            'share_type' => PartnerShareTypeEnum::Fixed,
        ]);

        $tax_record = TaxRecord::factory()->for($main_business)->create();
        $transaction = Transaction::create([
            'id' => 'TXN-'.fake()->uuid(),
            'business_id' => $main_business->id,
            'tax_record_id' => $tax_record->id,
            'amount' => 1000.00,
            'reference_number' => Transaction::generateReferenceNumber(),
            'type' => TransactionTypeEnum::TaxRemittance,
            'transaction_date' => now(),
            'status' => TransactionStatusEnum::Completed,
        ]);

        $partner_1_initial_balance = $partner_business_1->balanceFloatNum;
        $partner_2_initial_balance = $partner_business_2->balanceFloatNum;

        app(SplitRevenueAction::class)->handle($transaction);

        $partner_business_1->refresh();
        $partner_business_2->refresh();

        // Assert partner 1 received fixed amount (150.00)
        $this->assertEquals($partner_1_initial_balance + 150.00, $partner_business_1->balanceFloatNum);

        // Assert partner 2 received fixed amount (75.00)
        $this->assertEquals($partner_2_initial_balance + 75.00, $partner_business_2->balanceFloatNum);
    }

    public function test_does_nothing_when_no_partners_available(): void
    {
        $main_business = Business::factory()->create();

        $tax_record = TaxRecord::factory()->for($main_business)->create();
        $transaction = Transaction::create([
            'id' => 'TXN-'.fake()->uuid(),
            'business_id' => $main_business->id,
            'tax_record_id' => $tax_record->id,
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

        // Test passes if no exceptions are thrown
        $this->expectNotToPerformAssertions();
    }

    public function test_uses_dependency_injection_correctly(): void
    {
        $get_available_partners_action = $this->createMock(GetAvailablePartnersAction::class);
        $deposit_action = $this->createMock(DepositAction::class);

        $get_available_partners_action
            ->expects($this->once())
            ->method('handle')
            ->willReturn(new Collection);

        $deposit_action
            ->expects($this->never())
            ->method('handle');

        $split_revenue_action = new SplitRevenueAction(
            $get_available_partners_action,
            $deposit_action
        );

        $business = Business::factory()->create();
        $tax_record = TaxRecord::factory()->for($business)->create();
        $transaction = Transaction::create([
            'id' => 'TXN-'.fake()->uuid(),
            'business_id' => $business->id,
            'tax_record_id' => $tax_record->id,
            'amount' => 1000.00,
            'reference_number' => Transaction::generateReferenceNumber(),
            'type' => TransactionTypeEnum::TaxRemittance,
            'transaction_date' => now(),
            'status' => TransactionStatusEnum::Completed,
        ]);

        $split_revenue_action->handle($transaction);
    }

    public function test_handles_mixed_share_types(): void
    {
        $main_business = Business::factory()->create();
        $partner_business_1 = Business::factory()->create();
        $partner_business_2 = Business::factory()->create();

        // Mix of percentage and fixed shares
        Partner::create([
            'id' => fake()->uuid(),
            'business_id' => $partner_business_1->id,
            'shares' => 25.0, // 25%
            'share_type' => PartnerShareTypeEnum::Percentage,
        ]);

        Partner::create([
            'id' => fake()->uuid(),
            'business_id' => $partner_business_2->id,
            'shares' => 100.0, // Fixed 100.00
            'share_type' => PartnerShareTypeEnum::Fixed,
        ]);

        $tax_record = TaxRecord::factory()->for($main_business)->create();
        $transaction = Transaction::create([
            'id' => 'TXN-'.fake()->uuid(),
            'business_id' => $main_business->id,
            'tax_record_id' => $tax_record->id,
            'amount' => 800.00,
            'reference_number' => Transaction::generateReferenceNumber(),
            'type' => TransactionTypeEnum::TaxRemittance,
            'transaction_date' => now(),
            'status' => TransactionStatusEnum::Completed,
        ]);

        $partner_1_initial_balance = $partner_business_1->balanceFloatNum;
        $partner_2_initial_balance = $partner_business_2->balanceFloatNum;

        app(SplitRevenueAction::class)->handle($transaction);

        $partner_business_1->refresh();
        $partner_business_2->refresh();

        // Partner 1: 25% of 800.00 = 200.00
        $this->assertEquals($partner_1_initial_balance + 200.00, $partner_business_1->balanceFloatNum);

        // Partner 2: Fixed 100.00
        $this->assertEquals($partner_2_initial_balance + 100.00, $partner_business_2->balanceFloatNum);
    }
}
