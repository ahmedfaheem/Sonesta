<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CheckoutReservationRequest;
use App\Models\Client;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class ReservationController extends Controller
{
    public function availableRooms(Request $request): Response
    {
        $client = $this->approvedClientOrFail($request);

        $rooms = Room::query()
            ->doesntHave('reservations')
            ->with(['floor:id,name,number'])
            ->orderBy('number')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Room $room) => [
                'id' => $room->id,
                'number' => $room->number,
                'capacity' => $room->capacity,
                'price' => $room->price,
                'price_dollars' => number_format($room->price / 100, 2, '.', ''),
                'floor_name' => $room->floor?->name,
                'floor_number' => $room->floor?->number,
            ]);

        return Inertia::render('Client/Rooms/Index', [
            'rooms' => $rooms,
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
            ],
        ]);
    }

    public function create(Request $request, Room $room): Response|RedirectResponse
    {
        $this->approvedClientOrFail($request);

        if ($room->reservations()->exists()) {
            return redirect()
                ->route('client.rooms.index')
                ->with('error', 'This room is no longer available.');
        }

        return Inertia::render('Client/Reservations/Create', [
            'room' => [
                'id' => $room->id,
                'number' => $room->number,
                'capacity' => $room->capacity,
                'price_dollars' => number_format($room->price / 100, 2, '.', ''),
                'floor_name' => $room->floor?->name,
            ],
        ]);
    }

    public function checkout(CheckoutReservationRequest $request, Room $room): RedirectResponse|HttpResponse
    {
        $client = $this->approvedClientOrFail($request);
        $accompanyNumber = $request->integer('accompany_number');

        if ($room->reservations()->exists()) {
            return back()->withErrors([
                'room' => 'This room has already been reserved.',
            ]);
        }

        if ($accompanyNumber > $room->capacity) {
            return back()->withErrors([
                'accompany_number' => 'Accompany number cannot exceed room capacity.',
            ]);
        }

        try {
            $stripe = new StripeClient((string) config('services.stripe.secret'));
            $currency = (string) config('services.stripe.currency', 'usd');
            $floorName = $room->floor?->name ?? 'Unknown';

            $session = $stripe->checkout->sessions->create([
                'mode' => 'payment',
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'unit_amount' => $room->price,
                        'product_data' => [
                            'name' => "Room #{$room->number} Reservation",
                            'description' => "Floor: {$floorName}",
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'client_id' => (string) $client->id,
                    'room_id' => (string) $room->id,
                    'accompany_number' => (string) $accompanyNumber,
                ],
                'success_url' => route('client.reservations.success', absolute: true).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('client.reservations.cancel', ['room' => $room->id], absolute: true),
            ]);
        } catch (ApiErrorException $exception) {
            return back()->withErrors([
                'stripe' => 'Unable to start payment checkout right now. Please try again.',
            ]);
        }

        return Inertia::location($session->url);
    }

    public function success(Request $request): RedirectResponse
    {
        $client = $this->approvedClientOrFail($request);
        $sessionId = (string) $request->query('session_id', '');

        if ($sessionId === '') {
            return redirect()
                ->route('client.rooms.index')
                ->with('error', 'Missing checkout session id.');
        }

        try {
            $stripe = new StripeClient((string) config('services.stripe.secret'));
            $checkoutSession = $stripe->checkout->sessions->retrieve($sessionId);
        } catch (ApiErrorException $exception) {
            return redirect()
                ->route('client.rooms.index')
                ->with('error', 'Could not verify your payment session.');
        }

        if (($checkoutSession->payment_status ?? null) !== 'paid') {
            return redirect()
                ->route('client.rooms.index')
                ->with('error', 'Payment has not been completed.');
        }

        $metadata = $checkoutSession->metadata;
        $roomId = (int) ($metadata->room_id ?? 0);
        $metadataClientId = (int) ($metadata->client_id ?? 0);
        $accompanyNumber = (int) ($metadata->accompany_number ?? 0);

        if ($metadataClientId !== $client->id || $roomId <= 0) {
            return redirect()
                ->route('client.rooms.index')
                ->with('error', 'Invalid reservation session data.');
        }

        $room = Room::query()->find($roomId);

        if (! $room) {
            return redirect()
                ->route('client.rooms.index')
                ->with('error', 'Room no longer exists.');
        }

        if ($accompanyNumber > $room->capacity) {
            return redirect()
                ->route('client.rooms.index')
                ->with('error', 'Reservation could not be completed because room capacity changed.');
        }

        $alreadyReserved = Reservation::query()
            ->where('room_id', $room->id)
            ->where(function ($query) use ($checkoutSession): void {
                $query
                    ->whereNull('checkout_session_id')
                    ->orWhere('checkout_session_id', '!=', (string) $checkoutSession->id);
            })
            ->exists();

        if ($alreadyReserved) {
            return redirect()
                ->route('client.rooms.index')
                ->with('error', 'This room has already been reserved. Please contact support for payment assistance.');
        }

        DB::transaction(function () use ($client, $room, $accompanyNumber, $checkoutSession): void {
            Reservation::query()->firstOrCreate(
                ['checkout_session_id' => (string) $checkoutSession->id],
                [
                    'client_id' => $client->id,
                    'room_id' => $room->id,
                    'accompany_number' => $accompanyNumber,
                    'paid_price' => (int) ($checkoutSession->amount_total ?? $room->price),
                    'reservation_date' => now(),
                ]
            );
        });

        return redirect()
            ->route('client.reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    public function cancel(Request $request, ?Room $room = null): RedirectResponse
    {
        $this->approvedClientOrFail($request);

        if ($room) {
            return redirect()
                ->route('client.reservations.create', ['room' => $room->id])
                ->with('error', 'Payment was canceled.');
        }

        return redirect()
            ->route('client.rooms.index')
            ->with('error', 'Payment was canceled.');
    }

    public function index(Request $request): Response
    {
        $client = $this->approvedClientOrFail($request);

        $reservations = Reservation::query()
            ->where('client_id', $client->id)
            ->with('room:id,number')
            ->latest('reservation_date')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Reservation $reservation) => [
                'id' => $reservation->id,
                'accompany_number' => $reservation->accompany_number,
                'room_number' => $reservation->room?->number,
                'paid_price' => $reservation->paid_price,
                'paid_price_dollars' => number_format($reservation->paid_price / 100, 2, '.', ''),
                'reservation_date' => $reservation->reservation_date?->toISOString(),
            ]);

        return Inertia::render('Client/Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    protected function approvedClientOrFail(Request $request): Client
    {
        $user = $request->user();
        $client = $user?->clientProfile;

        abort_unless($user && $user->hasRole('client') && $client && $client->is_approved, 403);

        return $client;
    }
}
