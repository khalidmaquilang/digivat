<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Data;

use Spatie\LaravelData\Data;

class CancelTaxRecordData extends Data
{
    public function __construct(
        public string $cancel_reason,
    ) {}
}
