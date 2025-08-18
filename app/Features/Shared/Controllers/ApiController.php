<?php

declare(strict_types=1);

namespace App\Features\Shared\Controllers;

use App\Features\Business\Models\Business;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function resolveBusiness(): ?Business
    {
        /** @var ?Business $business */
        $business = request()->business;

        return $business;
    }
}
