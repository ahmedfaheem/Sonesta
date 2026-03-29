<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Receptionist/Dashboard', [
            'stats' => [
                'pending_clients' => User::role('client')
                    ->whereHas('clientProfile', fn ($query) => $query->where('is_approved', false))
                    ->count(),
                'approved_by_me' => Client::query()
                    ->where('approved_by', $user->id)
                    ->where('is_approved', true)
                    ->count(),
                'reservations' => Reservation::query()->count(),
            ],
        ]);
    }
}
