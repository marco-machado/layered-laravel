<?php

namespace Tests\Unit;

use App\Models\User;
use App\Policies\UserPolicy;
use PHPUnit\Framework\TestCase;

class UserPolicyTest extends TestCase
{
    protected UserPolicy $userPolicy;
    protected User $user;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userPolicy = new UserPolicy();

        // Arrange: Regular user
        $this->user = new User();
        $this->user->id = 1;

        // Arrange: Other user
        $this->otherUser = new User();
        $this->otherUser->id = 2;
    }

    public function test_should_return_true_when_user_can_view_any_user()
    {
        // Arrange

        // Act
        $result = $this->userPolicy->viewAny($this->user);

        // Assert
        $this->assertTrue($result);
    }

    public function test_should_return_true_when_user_can_view_own_user()
    {
        // Arrange

        // Act
        $result = $this->userPolicy->view($this->user, $this->user);

        // Assert
        $this->assertTrue($result);
    }

    public function test_should_return_false_when_user_cannot_view_another_user()
    {
        // Arrange

        // Act
        $result = $this->userPolicy->view($this->user, $this->otherUser);

        // Assert
        $this->assertFalse($result);
    }

    public function test_should_return_true_when_user_can_create_user()
    {
        // Arrange

        // Act
        $result = $this->userPolicy->create($this->user);

        // Assert
        $this->assertTrue($result);
    }

    public function test_should_return_true_when_user_can_update_own_user()
    {
        // Arrange

        // Act
        $result = $this->userPolicy->update($this->user, $this->user);

        // Assert
        $this->assertTrue($result);
    }

    public function test_should_return_false_when_user_cannot_update_another_user()
    {
        // Arrange

        // Act
        $result = $this->userPolicy->update($this->user, $this->otherUser);

        // Assert
        $this->assertFalse($result);
    }

    public function test_should_return_true_when_user_can_delete_another_user()
    {
        // Arrange

        // Act
        $result = $this->userPolicy->delete($this->user, $this->otherUser);

        // Assert
        $this->assertTrue($result);
    }

    public function test_should_return_false_when_user_cannot_delete_own_user()
    {
        // Arrange

        // Act
        $result = $this->userPolicy->delete($this->user, $this->user);

        // Assert
        $this->assertFalse($result);
    }
}
