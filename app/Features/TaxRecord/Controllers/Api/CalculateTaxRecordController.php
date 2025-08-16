<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Controllers\Api;

use App\Features\Shared\Controllers\ApiController;
use App\Features\TaxRecord\Actions\CalculateTaxAction;
use App\Features\TaxRecord\Data\CalculateTaxRecordData;
use App\Features\TaxRecord\Enums\CalculateTaxRecordModeEnum;
use App\Features\TaxRecord\Enums\CategoryTypeEnum;
use App\Features\User\Models\User;
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;

class CalculateTaxRecordController extends ApiController
{
    public function __construct(protected CalculateTaxAction $action) {}

    /**
     * Calculate Tax for Transaction
     *
     * Calculate tax amounts for a transaction based on item details, category type, and sales information.
     * This endpoint processes tax calculations according to Philippine tax regulations and returns detailed
     * tax breakdown including gross amount, taxable amount, tax amount, and total amount.
     *
     * @response array{
     * transaction_reference: string,
     * gross_amount: float,
     * taxable_amount: float,
     * tax_amount: float,
     * total_amount: float,
     * valid_until: datetime|null,
     * bir_receipt_id: string|null
     * }
     */
    #[HeaderParameter(name: 'Idempotency-Key', description: 'It must be a unique string, use UUID', required: true)]
    #[BodyParameter(name: 'mode', description: 'The calculation mode. Either "preview" for calculation without permanent record or "acknowledge" for creating a permanent tax record.', required: true, type: CalculateTaxRecordModeEnum::class)]
    #[BodyParameter(name: 'category_type', description: 'The business category type for tax calculation. Determines applicable tax rates.', required: true, type: CategoryTypeEnum::class)]
    #[BodyParameter(name: 'transaction_reference', description: 'Unique transaction identifier. Must be unique across all user transactions.', required: true, type: 'string')]
    #[BodyParameter(name: 'sales_date', description: 'The date and time when the sale occurred in ISO 8601 format.', required: true, type: 'string', format: 'date-time')]
    #[BodyParameter(name: 'items', description: 'Array of transaction items for tax calculation. Each item must include name, description, quantity, discount amount(it is the total discount amount), and unit_price.', required: true, type: 'array', example: [['name' => 'Premium Widget', 'description' => 'High-quality premium widget with advanced features', 'quantity' => 2, 'unit_price' => 5000, 'discount_amount' => 100]])]
    #[BodyParameter(name: 'order_discount', description: 'The discount amount applied to the order. Must be greater than or equal to 0. Defaults to 0.', required: false, type: 'float', example: 200)]
    #[Response(status: 404, description: 'User not found or unauthorized')]
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
