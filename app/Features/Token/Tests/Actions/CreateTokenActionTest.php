<?php

declare(strict_types=1);

namespace App\Features\Token\Tests\Actions;

use App\Features\Business\Models\Business;
use App\Features\Token\Actions\CreateTokenAction;
use App\Features\Token\Models\Token;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CreateTokenActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_token(): void
    {
        $business = Business::factory()->create();
        $tokenName = 'Test API Token';

        app(CreateTokenAction::class)->handle($business, $tokenName);

        $this->assertDatabaseHas('tokens', [
            'business_id' => $business->id,
            'name' => $tokenName,
        ]);
    }

    public function test_created_token_has_random_token_value(): void
    {
        $business = Business::factory()->create();
        $tokenName = 'Test API Token';

        app(CreateTokenAction::class)->handle($business, $tokenName);

        $token = Token::where('business_id', $business->id)
            ->where('name', $tokenName)
            ->first();

        $this->assertNotNull($token);
        $this->assertNotEmpty($token->token);
        $this->assertEquals(50, strlen((string) $token->token));
    }

    public function test_can_create_multiple_tokens_for_same_business(): void
    {
        $business = Business::factory()->create();

        app(CreateTokenAction::class)->handle($business, 'Token 1');
        app(CreateTokenAction::class)->handle($business, 'Token 2');

        $this->assertDatabaseCount('tokens', 2);
        $this->assertDatabaseHas('tokens', [
            'business_id' => $business->id,
            'name' => 'Token 1',
        ]);
        $this->assertDatabaseHas('tokens', [
            'business_id' => $business->id,
            'name' => 'Token 2',
        ]);
    }

    public function test_tokens_have_unique_token_values(): void
    {
        $business = Business::factory()->create();

        app(CreateTokenAction::class)->handle($business, 'Token 1');
        app(CreateTokenAction::class)->handle($business, 'Token 2');

        $tokens = Token::where('business_id', $business->id)->get();

        $this->assertEquals(2, $tokens->count());
        $firstToken = $tokens->first();
        $lastToken = $tokens->last();
        $this->assertNotNull($firstToken);
        $this->assertNotNull($lastToken);
        $this->assertNotEquals($firstToken->token, $lastToken->token);
    }
}
