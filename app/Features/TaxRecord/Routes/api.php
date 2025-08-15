<?php

declare(strict_types=1);

use App\Features\Shared\Middlewares\ValidateClientToken;
use App\Features\TaxRecord\Controllers\Api\CalculateTaxRecordController;
use Illuminate\Support\Facades\Route;

Route::middleware(ValidateClientToken::class)->group(function (): void {
    Route::post('/tax/calculate', CalculateTaxRecordController::class)
        ->name('tax.calculate');
});
