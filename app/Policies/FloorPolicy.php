<?php

namespace App\Policies;

use App\Models\Floor;
use App\Models\User;

class FloorPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function view(User $user, Floor $floor): bool
    {
        return $floor->manager_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'manager']);
    }

    public function update(User $user, Floor $floor): bool
    {
        return $floor->manager_id === $user->id;
    }

    public function delete(User $user, Floor $floor): bool
    {
        return $floor->manager_id === $user->id;
    }
}
