<?php

declare(strict_types=1);

namespace Features\TaxRecord\Controllers\Api;

use Features\Shared\Controllers\ApiController;
use Features\TaxRecord\Actions\CalculateTaxAction;
use Features\TaxRecord\Data\CalculateTaxRecordData;

class CalculateTaxRecordController extends ApiController
{
    public function __construct(protected CalculateTaxAction $action) {}

    /**
     * @throws \Throwable
     */
    public function __invoke(CalculateTaxRecordData $request)
    {
        $user = auth('sanctum')->user();
        abort_if($user === null, 404);

        $result = $this->action->handle($request, $user->id);

        return response()->json($result);
    }
}
