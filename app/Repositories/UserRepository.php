<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection<int, User>
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): User
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param array<string, mixed> $data
     * @return User
     */
    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return User
     */
    public function update(int $id, array $data): User
    {
        $record = $this->find($id);
        $record->update($data);

        return $record;
    }

    public function delete(int $id): ?bool
    {
        return $this->find($id)->delete();
    }
}