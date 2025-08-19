<?php

declare(strict_types=1);

namespace App\Features\Transaction\Actions;

use App\Features\BalanceTransaction\Actions\DepositAction;
use App\Features\Partner\Actions\GetAvailablePartnersAction;
use App\Features\Transaction\Models\Transaction;

class SplitRevenueAction
{
    public function __construct(
        protected GetAvailablePartnersAction $get_available_partners_action,
        protected DepositAction $deposit_action,
    ) {}

    public function handle(Transaction $transaction): void
    {
        $partners = $this->get_available_partners_action->handle();
        if ($partners->isEmpty()) {
            return;
        }

        $amount = $transaction->amount;
        foreach ($partners as $partner) {
            $share_amount = $partner->getShares($amount);

            $this->deposit_action->handle($partner, $share_amount, $transaction->id);
        }
    }
}
