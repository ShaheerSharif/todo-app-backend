<?php

namespace App\Services;

use App\Models\Todo;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TodoService
{
    public function getAllForUser(User $user, int $perPage = 15)
    {
        return $user->todos()
            ->latest()
            ->paginate($perPage);
    }

    public function create(User $user, array $data): Todo
    {
        return $user->todos()->create($data);
    }

    public function update(Todo $todo, array $data): Todo
    {
        if (array_key_exists('is_completed', $data)) {
            $data['completed_at'] = $data['is_completed'] ? now() : null;
        }

        $todo->update($data);

        return $todo;
    }

    public function delete(Todo $todo): void
    {
        $todo->delete();
    }

    /**
     * Ensure the given todo belongs to the given user.
     *
     * @throws AccessDeniedHttpException
     */
    public function authorizeTodo(Todo $todo, User $user): void
    {
        if ($todo->user_id !== $user->id) {
            throw new AccessDeniedHttpException('You do not own this todo.');
        }
    }
}
