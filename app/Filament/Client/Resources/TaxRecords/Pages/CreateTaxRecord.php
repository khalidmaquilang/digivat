<?php

declare(strict_types=1);

namespace App\Filament\Client\Resources\TaxRecords\Pages;

use App\Features\TaxRecord\Actions\GetTaxByCategoryAction;
use App\Features\TaxRecord\Actions\GetTotalGrossAmountAction;
use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\User\Models\User;
use App\Filament\Client\Resources\TaxRecords\TaxRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTaxRecord extends CreateRecord
{
    protected static string $resource = TaxRecordResource::class;

    /**
     * @var array<string, mixed>
     */
    protected array $tax_items = [];

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /** @var ?User $user */
        $user = auth()->user();
        abort_if($user === null, 404);

        $data['user_id'] = $user->id;

        /** @var array<string, mixed> $tax_items */
        $tax_items = $data['taxRecordItems'] ?? [];

        $data['gross_amount'] = app(GetTotalGrossAmountAction::class)->handle($tax_items);
        $data['taxable_amount'] = $data['gross_amount'] - $data['order_discount'];
        $data['tax_amount'] = app(GetTaxByCategoryAction::class)->handle($data['category_type'], $data['taxable_amount']);
        $data['status'] = TaxRecordStatusEnum::Acknowledged;

        $this->tax_items = $tax_items;
        unset($data['taxRecordItems']);

        return $data;
    }
}
