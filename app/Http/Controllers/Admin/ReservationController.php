<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Reservation::class);

        $reservations = Reservation::query()
            ->with([
                'client:id,user_id,name',
                'client.user:id,name',
                'room:id,number',
            ])
            ->latest('reservation_date')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Reservation $reservation) => $this->serializeReservation($reservation));

        return Inertia::render('Admin/Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    public function destroy(Reservation $reservation): RedirectResponse
    {
        $this->authorize('delete', $reservation);

        $reservation->delete();

        return back()->with('success', 'Reservation cancelled successfully.');
    }

    protected function serializeReservation(Reservation $reservation): array
    {
        $clientName = $reservation->client?->user?->name ?? $reservation->client?->name;

        return [
            'id' => $reservation->id,
            'client_name' => $clientName,
            'room_number' => $reservation->room?->number,
            'accompany_number' => $reservation->accompany_number,
            'paid_price' => $reservation->paid_price,
            'paid_price_dollars' => number_format($reservation->paid_price / 100, 2, '.', ''),
            'reservation_date' => $reservation->reservation_date?->toISOString(),
        ];
    }
}
