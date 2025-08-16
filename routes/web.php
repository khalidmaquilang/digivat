<?php

use App\Features\TaxRecord\Controllers\BirReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/bir-receipt/{taxRecord}', [BirReceiptController::class, 'show'])
    ->name('bir-receipt.show');
