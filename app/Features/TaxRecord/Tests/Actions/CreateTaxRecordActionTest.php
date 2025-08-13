<?php

declare(strict_types=1);

namespace Features\TaxRecord\Tests\Actions;

use App\Models\User;
use Tests\TestCase;

final class CreateTaxRecordActionTest extends TestCase
{
    public function test_can_create_tax_record(): void
    {
        User::factory()->create();

    }
}
