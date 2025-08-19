<?php

declare(strict_types=1);

namespace App\Features\BalanceTransaction\Tests\Actions;

use App\Features\BalanceTransaction\Actions\DepositAction;
use App\Features\Business\Models\Business;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DepositActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_deposit_to_wallet(): void
    {
        $business = Business::factory()->create();
        $amount = 100.50;
        $transaction_id = 'TXN-12345';

        $initial_balance = $business->balanceFloat;

        app(DepositAction::class)->handle($business, $amount, $transaction_id);

        $business->refresh();

        $this->assertEquals($initial_balance + $amount, $business->balanceFloat);
    }

    public function test_deposit_includes_transaction_id_in_meta(): void
    {
        $business = Business::factory()->create();
        $amount = 50.75;
        $transaction_id = 'TXN-67890';

        app(DepositAction::class)->handle($business, $amount, $transaction_id);

        $transactions = $business->walletTransactions;
        $this->assertCount(1, $transactions);

        $transaction = $transactions->first();
        $this->assertEquals($transaction_id, $transaction->meta['transaction_id']);
        $this->assertEquals($this->convertMoney($amount), $transaction->amount);
    }

    public function test_can_deposit_zero_amount(): void
    {
        $business = Business::factory()->create();
        $amount = 0.00;
        $transaction_id = 'TXN-00000';

        $initial_balance = $business->balanceFloat;

        app(DepositAction::class)->handle($business, $amount, $transaction_id);

        $business->refresh();

        $this->assertEquals($initial_balance, $business->balanceFloat);
    }

    public function test_can_deposit_large_amount(): void
    {
        $business = Business::factory()->create();
        $amount = 9999.99;
        $transaction_id = 'TXN-LARGE';

        $initial_balance = $business->balanceFloat;

        app(DepositAction::class)->handle($business, $amount, $transaction_id);

        $business->refresh();

        $this->assertEquals($initial_balance + $amount, $business->balanceFloat);
    }

    public function test_multiple_deposits_accumulate(): void
    {
        $business = Business::factory()->create();
        $initial_balance = $business->balanceFloat;

        app(DepositAction::class)->handle($business, 100.00, 'TXN-1');
        app(DepositAction::class)->handle($business, 200.00, 'TXN-2');
        app(DepositAction::class)->handle($business, 50.50, 'TXN-3');

        $business->refresh();

        $this->assertEquals($initial_balance + 350.50, $business->balanceFloat);
        $this->assertCount(3, $business->walletTransactions);
    }
}
