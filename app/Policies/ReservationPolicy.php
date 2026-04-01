<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('receptionist');
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $user->hasRole('receptionist');
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->hasRole('admin');
    }
}
