<?php

declare(strict_types=1);

namespace App\Features\TaxRecord\Tests\Controllers\Api;

use App\Features\TaxRecord\Enums\TaxRecordStatusEnum;
use App\Features\TaxRecord\Models\TaxRecord;
use App\Features\Token\Models\Token;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CancelTaxRecordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_cancel_tax_record_with_authenticated_user(): void
    {
        $user = User::factory()->create();
        $token = Token::factory()->create(['user_id' => $user->id]);
        $tax_record = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);

        $response = $this->postJson(
            route('api.tax.cancel', ['tax_record' => $tax_record->id]),
            [],
            ['Authorization' => 'Bearer '.$token->token]
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'status',
                'updated_at',
            ]);

        $this->assertDatabaseHas('tax_records', [
            'id' => $tax_record->id,
            'status' => TaxRecordStatusEnum::Cancelled->value,
        ]);
    }

    public function test_does_not_update_already_cancelled_tax_record(): void
    {
        $user = User::factory()->create();
        $token = Token::factory()->create(['user_id' => $user->id]);
        $tax_record = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Cancelled,
        ]);

        $original_updated_at = $tax_record->updated_at;

        $response = $this->postJson(
            route('api.tax.cancel', ['tax_record' => $tax_record->id]),
            [],
            ['Authorization' => 'Bearer '.$token->token]
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'status',
                'updated_at',
            ]);

        // Verify the updated_at timestamp wasn't changed
        $tax_record->refresh();
        $this->assertNotNull($original_updated_at);
        $this->assertNotNull($tax_record->updated_at);
        $this->assertEquals($original_updated_at->toDateTimeString(), $tax_record->updated_at->toDateTimeString());
    }

    public function test_prevents_cancelling_other_users_tax_record(): void
    {
        $owner_user = User::factory()->create();
        $other_user = User::factory()->create();
        $token = Token::factory()->create(['user_id' => $other_user->id]);

        $tax_record = TaxRecord::factory()->create([
            'user_id' => $owner_user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);

        $response = $this->postJson(
            route('api.tax.cancel', ['tax_record' => $tax_record->id]),
            [],
            ['Authorization' => 'Bearer '.$token->token]
        );

        $response->assertStatus(403);

        // Verify the record was not cancelled
        $this->assertDatabaseHas('tax_records', [
            'id' => $tax_record->id,
            'status' => TaxRecordStatusEnum::Acknowledged->value,
        ]);
    }

    public function test_returns_404_for_nonexistent_tax_record(): void
    {
        $user = User::factory()->create();
        $token = Token::factory()->create(['user_id' => $user->id]);
        $nonexistent_id = 'BIR-TX-999999999999';

        $response = $this->postJson(
            route('api.tax.cancel', ['tax_record' => $nonexistent_id]),
            [],
            ['Authorization' => 'Bearer '.$token->token]
        );

        $response->assertStatus(404);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $user = User::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);

        $response = $this->postJson(
            route('api.tax.cancel', ['tax_record' => $tax_record->id])
        );

        $response->assertStatus(401)
            ->assertJson(['error' => 'No token provided']);
    }

    public function test_invalid_token_returns_401(): void
    {
        $user = User::factory()->create();
        $tax_record = TaxRecord::factory()->create([
            'user_id' => $user->id,
            'status' => TaxRecordStatusEnum::Acknowledged,
        ]);

        $response = $this->postJson(
            route('api.tax.cancel', ['tax_record' => $tax_record->id]),
            [],
            ['Authorization' => 'Bearer invalid-token']
        );

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid token']);
    }
}
