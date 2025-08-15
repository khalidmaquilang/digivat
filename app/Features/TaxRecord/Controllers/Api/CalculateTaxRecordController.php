<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Controllers\Api;

use App\Features\Shared\Controllers\ApiController;
use App\Features\TaxRecord\Actions\CalculateTaxAction;
use App\Features\TaxRecord\Data\CalculateTaxRecordData;
use App\Features\User\Models\User;
use Illuminate\Http\JsonResponse;

class CalculateTaxRecordController extends ApiController
{
    public function __construct(protected CalculateTaxAction $action) {}

    /**
     * @throws \Throwable
     */
    public function __invoke(CalculateTaxRecordData $request): JsonResponse
    {
        /** @var ?User $user */
        $user = $this->resolveUser();
        abort_if($user === null, 404);

        // Get referer from the request
        $referer = request()->header('referer', '');

        $result = $this->action->handle($request, $user->id, $referer);

        return response()->json($result);
    }
}
