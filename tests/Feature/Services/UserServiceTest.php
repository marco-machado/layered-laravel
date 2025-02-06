<?php

namespace Tests\Feature\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected MockInterface $userRepository;
    protected UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->mock(UserRepositoryInterface::class);
        $this->userService = new UserService($this->userRepository);
    }

    #[Test]
    public function getAllUsers_ReturnsAllUsers(): void
    {
        // Arrange
        $expectedUsers = collect([
            new User(['id' => 1, 'name' => 'John', 'email' => 'john@example.com']),
            new User(['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com']),
        ]);

        $this->userRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn($expectedUsers);

        // Act
        $result = $this->userService->getAllUsers();

        // Assert
        $this->assertEquals($expectedUsers, $result);
    }

    public function getUserById_WithValidId_ReturnsUser(): void
    {
        // Arrange
        $expectedUser = new User(['id' => 1, 'name' => 'John', 'email' => 'john@example.com']);

        $this->userRepository
            ->shouldReceive('find')
            ->once()
            ->andReturn($expectedUser);

        // Act
        $result = $this->userService->getUserById(1);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($expectedUser, $result);
    }

    #[Test]
    public function getUserById_WithInvalidId_ThrowsException(): void
    {
        // Arrange
        $this->userRepository
            ->shouldReceive('find')
            ->once()
            ->andThrow(new ModelNotFoundException());

        // Assert
        $this->expectException(ModelNotFoundException::class);

        // Act
        $this->userService->getUserById(1);
    }

    #[Test]
    public function createUser_WithValidData_ReturnsUser(): void
    {
        // Arrange
        $userData = ['name' => 'John', 'email' => 'john@example.com'];

        $this->userRepository
            ->shouldReceive('create')
            ->once()
            ->andReturn(new User(['id' => 1, 'name' => 'John', 'email' => 'john@example.com']));

        // Act
        $result = $this->userService->createUser($userData);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($userData['name'], $result->name);
        $this->assertEquals($userData['email'], $result->email);
    }

    #[Test]
    public function updateUser_WithValidData_ReturnsUpdatedUser(): void
    {
        // Arrange
        $userId = 1;
        $userData = [
            'name' => 'John Updated',
            'email' => 'john.updated@example.com',
        ];

        $expectedUser = new User([
            'id' => $userId,
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);

        $this->userRepository
            ->shouldReceive('update')
            ->once()
            ->withArgs(function ($id, $data) use ($userId, $userData) {
                return $id === $userId &&
                    $data['name'] === $userData['name'] &&
                    $data['email'] === $userData['email'];
            })
            ->andReturn($expectedUser);

        // Act
        $result = $this->userService->updateUser($userId, $userData);

        // Assert
        $this->assertEquals($expectedUser, $result);
    }

    #[Test]
    public function deleteUser_WithValidId_ReturnsTrue(): void
    {
        // Arrange
        $userId = 1;

        $this->userRepository
            ->shouldReceive('delete')
            ->once()
            ->with($userId)
            ->andReturn(true);

        // Act
        $result = $this->userService->deleteUser($userId);

        // Assert
        $this->assertTrue($result);
    }
}
