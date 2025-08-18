<?php

declare(strict_types=1);

namespace App\Features\User\Tests\Actions;

use App\Features\User\Actions\GetUserByEmailAction;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class GetUserByEmailActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_user_when_email_exists(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $action = new GetUserByEmailAction;

        // Act
        $result = $action->handle('test@example.com');

        // Assert
        $this->assertNotNull($result);
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
        $this->assertEquals('test@example.com', $result->email);
        $this->assertEquals('John', $result->first_name);
        $this->assertEquals('Doe', $result->last_name);
    }

    public function test_it_returns_null_when_email_does_not_exist(): void
    {
        // Arrange
        $action = new GetUserByEmailAction;

        // Act
        $result = $action->handle('nonexistent@example.com');

        // Assert
        $this->assertNull($result);
    }

    public function test_it_returns_null_when_email_is_empty(): void
    {
        // Arrange
        $action = new GetUserByEmailAction;

        // Act
        $result = $action->handle('');

        // Assert
        $this->assertNull($result);
    }

    public function test_it_returns_correct_user_when_multiple_exist(): void
    {
        // Arrange
        User::factory()->create(['email' => 'user1@example.com']);
        $targetUser = User::factory()->create([
            'email' => 'user2@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);
        User::factory()->create(['email' => 'user3@example.com']);

        $action = new GetUserByEmailAction;

        // Act
        $result = $action->handle('user2@example.com');

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($targetUser->id, $result->id);
        $this->assertEquals('user2@example.com', $result->email);
        $this->assertEquals('Jane', $result->first_name);
        $this->assertEquals('Smith', $result->last_name);
    }

    public function test_it_ignores_soft_deleted_users(): void
    {
        // Arrange
        $user = User::factory()->create(['email' => 'deleted@example.com']);
        $user->delete(); // Soft delete the user

        $action = new GetUserByEmailAction;

        // Act
        $result = $action->handle('deleted@example.com');

        // Assert
        $this->assertNull($result);
    }

    public function test_it_is_case_sensitive(): void
    {
        // Arrange
        User::factory()->create(['email' => 'Test@Example.com']);

        $action = new GetUserByEmailAction;

        // Act
        $result = $action->handle('test@example.com');

        // Assert
        $this->assertNull($result);
    }
}
