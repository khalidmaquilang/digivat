<?php

declare(strict_types=1);

namespace Features\TaxRecord\Controllers\Api;

use Features\Shared\Controllers\ApiController;
use Features\TaxRecord\Actions\CalculateTaxAction;
use Features\TaxRecord\Data\CalculateTaxRecordData;
use Features\User\Models\User;
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

        $result = $this->action->handle($request, $user->id);

        return response()->json($result);
    }
}
