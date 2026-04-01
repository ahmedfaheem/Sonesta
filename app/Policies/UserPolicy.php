<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function viewPendingClients(User $user): bool
    {
        return $user->hasRole('receptionist');
    }

    public function viewApprovedClients(User $user): bool
    {
        return $user->hasRole('receptionist');
    }

    public function approveClient(User $user, User $client): bool
    {
        return $user->hasRole('receptionist')
            && $client->hasRole('client')
            && $client->clientProfile()->exists()
            && ! $client->clientProfile?->is_approved;
    }

    public function viewAnyReceptionists(User $user): bool
    {
        return $user->hasRole('manager');
    }

    public function createReceptionist(User $user): bool
    {
        return $user->hasRole('manager');
    }

    public function updateReceptionist(User $user, User $receptionist): bool
    {
        return $this->viewReceptionist($user, $receptionist)
            && $user->hasRole('manager')
            && $receptionist->hasRole('receptionist');
    }

    public function viewReceptionist(User $user, User $receptionist): bool
    {
        return $user->hasRole('manager')
            && $receptionist->hasRole('receptionist')
            && (int) $receptionist->created_by === (int) $user->id;
    }

    public function deleteReceptionist(User $user, User $receptionist): bool
    {
        return $this->updateReceptionist($user, $receptionist);
    }

    public function banReceptionist(User $user, User $receptionist): bool
    {
        return $this->updateReceptionist($user, $receptionist);
    }

    public function viewAnyClients(User $user): bool
    {
        return $user->hasRole('manager');
    }

    public function createClient(User $user): bool
    {
        return $user->hasRole('manager');
    }

    public function updateClient(User $user, User $client): bool
    {
        return $this->viewClient($user, $client)
            && $user->hasRole('manager')
            && $client->hasRole('client');
    }

    public function viewClient(User $user, User $client): bool
    {
        return $user->hasRole('manager')
            && $client->hasRole('client')
            && (int) $client->created_by === (int) $user->id;
    }

    public function deleteClient(User $user, User $client): bool
    {
        return $this->updateClient($user, $client);
    }
}
