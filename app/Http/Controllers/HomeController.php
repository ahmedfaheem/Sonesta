<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        $rooms = collect();
        $featuredRooms = collect();
        $totalRooms = 0;
        $totalReservations = 0;
        $averagePrice = '0.00';

        if (Schema::hasTable('rooms')) {
            try {
                $roomQuery = Room::query();

                $totalRooms = $roomQuery->count();

                $avg = (float) $roomQuery->avg('price');
                $averagePrice = number_format($avg / 100, 2, '.', '');

                $rooms = Room::query()
                    ->latest()
                    ->limit(6)
                    ->get(['id', 'number', 'capacity', 'price'])
                    ->map(fn (Room $room) => [
                        'id' => $room->id,
                        'number' => $room->number,
                        'capacity' => $room->capacity,
                        'price' => $room->price,
                        'price_dollars' => number_format($room->price / 100, 2, '.', ''),
                        'image_url' => null,
                    ]);

                if (Schema::hasTable('reservations')) {
                    $featuredRooms = Room::query()
                        ->withCount('reservations')
                        ->orderByDesc('reservations_count')
                        ->limit(3)
                        ->get(['id', 'number', 'capacity', 'price'])
                        ->map(fn (Room $room) => [
                            'id' => $room->id,
                            'number' => $room->number,
                            'capacity' => $room->capacity,
                            'price' => $room->price,
                            'price_dollars' => number_format($room->price / 100, 2, '.', ''),
                            'reservations_count' => $room->reservations_count,
                            'image_url' => null,
                        ]);
                }
            } catch (QueryException) {
                $rooms = collect();
                $featuredRooms = collect();
                $totalRooms = 0;
                $averagePrice = '0.00';
            }
        }

        if (Schema::hasTable('reservations')) {
            try {
                $totalReservations = Reservation::query()->count();
            } catch (QueryException) {
                $totalReservations = 0;
            }
        }

        return Inertia::render('Welcome', [
            'canLogin' => ! $request->user() && Route::has('login'),
            'canRegister' => ! $request->user() && Route::has('register'),
            'rooms' => $rooms,
            'featuredRooms' => $featuredRooms,
            'stats' => [
                'totalRooms' => $totalRooms,
                'totalReservations' => $totalReservations,
                'averagePrice' => $averagePrice,
            ],
        ]);
    }
}
