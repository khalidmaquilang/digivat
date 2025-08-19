<?php

declare(strict_types=1);

namespace App\Features\User\Tests\Actions;

use App\Features\Business\Models\Business;
use App\Features\User\Actions\KickUserFromCompanyAction;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class KickUserFromCompanyActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_removes_user_from_business_when_member(): void
    {
        // Arrange
        $business = Business::factory()->create();
        $user = User::factory()->create();
        $business->members()->attach($user);

        // Verify user is initially attached
        $this->assertTrue($business->members()->where('user_id', $user->id)->exists());

        $action = new KickUserFromCompanyAction;

        // Act
        $action->handle($business, $user);

        // Assert
        $this->assertFalse($business->members()->where('user_id', $user->id)->exists());
        $this->assertDatabaseMissing('business_user', [
            'business_id' => $business->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_it_handles_user_not_member_of_business_gracefully(): void
    {
        // Arrange
        $business = Business::factory()->create();
        $user = User::factory()->create();

        // Verify user is not attached initially
        $this->assertFalse($business->members()->where('user_id', $user->id)->exists());

        $action = new KickUserFromCompanyAction;

        // Act - This should not throw an exception
        $action->handle($business, $user);

        // Assert - Still no relationship
        $this->assertFalse($business->members()->where('user_id', $user->id)->exists());
    }

    public function test_it_only_removes_specified_user_from_business(): void
    {
        // Arrange
        $business = Business::factory()->create();
        $userToRemove = User::factory()->create();
        $userToKeep = User::factory()->create();

        $business->members()->attach([$userToRemove->id, $userToKeep->id]);

        // Verify both users are initially attached
        $this->assertTrue($business->members()->where('user_id', $userToRemove->id)->exists());
        $this->assertTrue($business->members()->where('user_id', $userToKeep->id)->exists());

        $action = new KickUserFromCompanyAction;

        // Act
        $action->handle($business, $userToRemove);

        // Assert
        $this->assertFalse($business->members()->where('user_id', $userToRemove->id)->exists());
        $this->assertTrue($business->members()->where('user_id', $userToKeep->id)->exists());
    }

    public function test_it_handles_multiple_businesses_correctly(): void
    {
        // Arrange
        $business1 = Business::factory()->create();
        $business2 = Business::factory()->create();
        $user = User::factory()->create();

        $business1->members()->attach($user);
        $business2->members()->attach($user);

        // Verify user is attached to both businesses
        $this->assertTrue($business1->members()->where('user_id', $user->id)->exists());
        $this->assertTrue($business2->members()->where('user_id', $user->id)->exists());

        $action = new KickUserFromCompanyAction;

        // Act - Remove user from only business1
        $action->handle($business1, $user);

        // Assert
        $this->assertFalse($business1->members()->where('user_id', $user->id)->exists());
        $this->assertTrue($business2->members()->where('user_id', $user->id)->exists());
    }

    public function test_it_can_remove_user_multiple_times_without_error(): void
    {
        // Arrange
        $business = Business::factory()->create();
        $user = User::factory()->create();
        $business->members()->attach($user);

        $action = new KickUserFromCompanyAction;

        // Act - Remove user first time
        $action->handle($business, $user);
        $this->assertFalse($business->members()->where('user_id', $user->id)->exists());

        // Act - Remove user second time (should not cause error)
        $action->handle($business, $user);

        // Assert
        $this->assertFalse($business->members()->where('user_id', $user->id)->exists());
    }
}
