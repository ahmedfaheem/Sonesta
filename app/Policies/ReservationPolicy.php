<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('receptionist');
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $user->hasRole('receptionist');
    }
}
