<?php

namespace Tests\Feature\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = app(UserRepositoryInterface::class);
    }

    #[Test]
    public function all_ReturnsAllUsers(): void
    {
        // Arrange
        $users = User::factory()->count(3)->create();

        // Act
        $result = $this->userRepository->all();

        // Assert
        $this->assertCount(3, $result);
        $this->assertEquals($users->toArray(), $result->toArray());
    }

    #[Test]
    public function find_WithValidId_ReturnsUser(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->userRepository->find($user->id);

        // Assert
        $this->assertEquals($user->toArray(), $result->toArray());
    }

    #[Test]
    public function find_WithInvalidId_ThrowsException(): void
    {
        // Arrange
        $this->expectException(ModelNotFoundException::class);

        // Act
        $this->userRepository->find(999);
    }

    #[Test]
    public function create_WithValidData_ReturnsUser(): void
    {
        // Arrange
        $data = ['name' => 'John Doe', 'email' => 'john@example.com', 'password' => 'password'];

        // Act
        $result = $this->userRepository->create($data);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertDatabaseHas('users', Arr::only($data, ['name', 'email']));
    }

    #[Test]
    public function update_WithValidIdAndData_ReturnsUpdatedUser(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $return = $this->userRepository->update($user->id, ['name' => 'Jane Doe']);

        // Assert
        $this->assertEquals('Jane Doe', $return->name);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Jane Doe']);
    }

    #[Test]
    public function delete_WithValidId_ReturnsTrue(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $result = $this->userRepository->delete($user->id);

        // Assert
        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    #[Test]
    public function delete_WithInvalidId_ThrowsException(): void
    {
        // Arrange
        $this->expectException(ModelNotFoundException::class);

        // Act
        $this->userRepository->delete(999);
    }

    #[Test]
    public function delete_WithNonexistentUser_ReturnsNull(): void
    {
        // This will never happen because the findOrFail method will throw an exception if the user does not exist.
        // I'm leaving this here for documentation purposes.

        // Arrange
        /** @var User $userModel */
        $userModel = $this->mock(User::class, function ($mock) {
            $mock->shouldReceive('findOrFail')->andReturn(new User());
        });

        $repositoryService = new UserRepository($userModel);

        // Act
        $result = $repositoryService->delete(1);

        // Assert
        $this->assertNull($result);
    }
}
