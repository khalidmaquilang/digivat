<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Controllers\Web;

use App\Features\TaxRecord\Models\TaxRecord;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class BirReceiptController extends Controller
{
    public function __invoke(TaxRecord $tax_record): View
    {
        $tax_record->load('taxRecordItems');

        return view('TaxRecord.Views.bir-receipt', ['taxRecord' => $tax_record]);
    }
}
