<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Controllers\Api;

use App\Features\Business\Models\Business;
use App\Features\Shared\Controllers\ApiController;
use App\Features\TaxRecord\Actions\CancelTaxRecordAction;
use App\Features\TaxRecord\Models\TaxRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CancelTaxRecordController extends ApiController
{
    public function __construct(protected CancelTaxRecordAction $action) {}

    public function __invoke(Request $request, string $tax_record): JsonResponse
    {
        /** @var ?Business $business */
        $business = $this->resolveBusiness();
        abort_if($business === null, 404);

        // First check if the tax record exists at all
        $tax_record_model = TaxRecord::withoutGlobalScopes()->where('id', $tax_record)->first();
        abort_if($tax_record_model === null, 404, 'Tax record not found');

        // Then check if it belongs to the authenticated business
        if ($tax_record_model->business_id !== $business->id) {
            abort(403, 'Unauthorized access to tax record');
        }

        $cancel_reason = $request->input('cancel_reason', 'Cancelled via API');
        $result = $this->action->handle($tax_record_model, $cancel_reason);

        return response()->json($result);
    }
}
