<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Controllers\Api;

use App\Features\Business\Models\Business;
use App\Features\Shared\Controllers\ApiController;
use App\Features\TaxRecord\Actions\CancelTaxRecordAction;
use App\Features\TaxRecord\Data\CancelTaxRecordData;
use App\Features\TaxRecord\Models\TaxRecord;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\PathParameter;
use Illuminate\Http\JsonResponse;

class CancelTaxRecordController extends ApiController
{
    public function __construct(protected CancelTaxRecordAction $action) {}

    /**
     * Cancel Tax Record
     *
     * Cancels an existing tax record that belongs to the authenticated user.
     * Only tax records in 'preview' or 'acknowledged' status can be cancelled.
     * Once cancelled, the tax record status will be updated to 'cancelled' and cannot be reversed.
     */
    #[PathParameter(name: 'tax_record', description: 'The UUID of the tax record to cancel', required: true, type: 'string')]
    #[BodyParameter(name: 'cancel_reason', description: 'The reason for cancelling this tax record', required: true, type: 'string', example: 'Order cancelled by customer')]
    public function __invoke(CancelTaxRecordData $request, string $tax_record): JsonResponse
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

        $this->action->handle($tax_record_model, $request->cancel_reason);

        return response()->json([
            'message' => 'Tax record cancelled successfully',
        ]);
    }
}
