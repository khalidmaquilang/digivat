<?php

declare(strict_types=1);

namespace App\Features\Partner\Tests\Actions;

use App\Features\Business\Models\Business;
use App\Features\Partner\Actions\GetAvailablePartnersAction;
use App\Features\Partner\Helpers\PartnerCacheHelper;
use App\Features\Partner\Models\Partner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

final class GetAvailablePartnersActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_available_partners_from_cache(): void
    {
        $business = Business::factory()->create();
        $partners = Partner::factory()->count(3)->for($business)->create();

        $result = app(GetAvailablePartnersAction::class)->handle();

        $this->assertCount(3, $result);
        $this->assertEquals($partners->pluck('id')->sort(), $result->pluck('id')->sort());
    }

    public function test_returns_empty_collection_when_no_partners_exist(): void
    {
        $result = app(GetAvailablePartnersAction::class)->handle();

        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function test_uses_cache_for_subsequent_calls(): void
    {
        $business = Business::factory()->create();
        Partner::factory()->count(2)->for($business)->create();

        // Clear any existing cache
        PartnerCacheHelper::flush();

        // First call should hit the database and cache the result
        $result1 = app(GetAvailablePartnersAction::class)->handle();

        // Second call should use cached result
        $result2 = app(GetAvailablePartnersAction::class)->handle();

        // Both results should be identical
        $this->assertEquals($result1->pluck('id')->sort(), $result2->pluck('id')->sort());
        $this->assertCount(2, $result1);
        $this->assertCount(2, $result2);
    }

    public function test_partners_are_loaded_with_business_relationship(): void
    {
        $business = Business::factory()->create();
        Partner::factory()->for($business)->create();

        $result = app(GetAvailablePartnersAction::class)->handle();

        $this->assertTrue($result->first()->relationLoaded('business'));
        $this->assertEquals($business->id, $result->first()->business->id);
    }
}
