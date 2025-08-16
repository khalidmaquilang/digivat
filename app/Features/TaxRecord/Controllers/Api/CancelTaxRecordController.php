<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Controllers\Api;

use App\Features\Shared\Controllers\ApiController;
use App\Features\TaxRecord\Actions\CancelTaxRecordAction;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\User\Models\User;
use Dedoc\Scramble\Attributes\PathParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
    public function __invoke(Request $request, string $tax_record): JsonResponse
    {
        /** @var ?User $user */
        $user = $this->resolveUser();
        abort_if($user === null, 404);

        // First check if the tax record exists at all
        $tax_record_model = TaxRecord::withoutGlobalScopes()->where('id', $tax_record)->first();
        abort_if($tax_record_model === null, 404, 'Tax record not found');

        // Then check if it belongs to the authenticated user
        if ($tax_record_model->user_id !== $user->id) {
            abort(403, 'Unauthorized access to tax record');
        }

        $result = $this->action->handle($tax_record_model);

        return response()->json($result);
    }
}
