<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Controllers;

use App\Features\TaxRecord\Models\TaxRecord;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BirReceiptController extends Controller
{
    public function show(TaxRecord $taxRecord): View
    {
        $taxRecord->load('taxRecordItems');

        return view('bir-receipt', ['taxRecord' => $taxRecord]);
    }
}
