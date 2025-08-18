<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Features\Business\Models\Business;
use App\Features\InviteUser\Actions\GetInvitationByCode;
use App\Features\InviteUser\Models\InviteUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetInvitationByCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_invitation_when_code_exists(): void
    {
        // Arrange
        $business = Business::factory()->create();
        $invitation = InviteUser::factory()->create([
            'business_id' => $business->id,
            'email' => 'test@example.com',
            'code' => 'TEST123',
        ]);

        $action = new GetInvitationByCode;

        // Act
        $result = $action->handle('TEST123');

        // Assert
        $this->assertNotNull($result);
        $this->assertInstanceOf(InviteUser::class, $result);
        $this->assertEquals($invitation->id, $result->id);
        $this->assertEquals('TEST123', $result->code);
        $this->assertEquals('test@example.com', $result->email);
    }

    public function test_it_returns_null_when_code_does_not_exist(): void
    {
        // Arrange
        $action = new GetInvitationByCode;

        // Act
        $result = $action->handle('NONEXISTENT');

        // Assert
        $this->assertNull($result);
    }

    public function test_it_returns_null_when_code_is_empty(): void
    {
        // Arrange
        $action = new GetInvitationByCode;

        // Act
        $result = $action->handle('');

        // Assert
        $this->assertNull($result);
    }

    public function test_it_returns_correct_invitation_when_multiple_exist(): void
    {
        // Arrange
        $business = Business::factory()->create();
        InviteUser::factory()->create([
            'business_id' => $business->id,
            'code' => 'CODE1',
        ]);
        $targetInvitation = InviteUser::factory()->create([
            'business_id' => $business->id,
            'code' => 'CODE2',
        ]);
        InviteUser::factory()->create([
            'business_id' => $business->id,
            'code' => 'CODE3',
        ]);

        $action = new GetInvitationByCode;

        // Act
        $result = $action->handle('CODE2');

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($targetInvitation->id, $result->id);
        $this->assertEquals('CODE2', $result->code);
    }
}
