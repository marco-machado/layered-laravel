<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    /**
     * @return Collection<int, User>
     */
    public function all(): Collection;

    public function find(int $id): User;

    /**
     * @param array<string, mixed> $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return User
     */
    public function update(int $id, array $data): User;

    public function delete(int $id): ?bool;
}