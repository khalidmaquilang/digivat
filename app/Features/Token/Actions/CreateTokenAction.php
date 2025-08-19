<?php

declare(strict_types=1);

namespace App\Features\Token\Actions;

use App\Features\Business\Models\Business;

class CreateTokenAction
{
    public function handle(Business $business, string $name): void
    {
        $business->tokens()->create([
            'name' => $name,
        ]);
    }
}
