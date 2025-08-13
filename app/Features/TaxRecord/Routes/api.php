<?php

declare(strict_types=1);

use Features\TaxRecord\Controllers\Api\CalculateTaxRecordController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/tax/calculate', CalculateTaxRecordController::class)
        ->name('tax.calculate');
});
