<?php

declare(strict_types=1);

namespace App\Features\BalanceTransaction\Actions;

use Bavix\Wallet\Interfaces\WalletFloat;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;

class DepositAction
{
    /**
     * @throws ExceptionInterface
     */
    public function handle(WalletFloat $wallet, float $amount, string $transaction_id): void
    {
        $wallet->depositFloat($amount, [
            'transaction_id' => $transaction_id,
        ]);
    }
}
