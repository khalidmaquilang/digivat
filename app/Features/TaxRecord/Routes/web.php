<?php

declare(strict_types=1);

use App\Features\TaxRecord\Controllers\Web\BirReceiptController;

Route::get('/bir-receipt/{tax_record}', BirReceiptController::class)
    ->name('bir-receipt.show');
