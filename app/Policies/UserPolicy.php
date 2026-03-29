<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
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
}
