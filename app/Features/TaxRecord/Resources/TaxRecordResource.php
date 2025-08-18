<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Resources;

use App\Features\TaxRecord\Models\TaxRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TaxRecord */
class TaxRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
