<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;

class RoomsController extends Controller
{
    public function index(): JsonResponse
    {
        $availableRooms = Room::query()
            ->available()
            ->with('floor:id,name,number')
            ->get()
            ->map(function (Room $room) {
                return [
                    'id' => $room->id,
                    'number' => $room->number,
                    'capacity' => $room->capacity,
                    'price' => number_format($room->price / 100, 2, '.', ''),
                    'floor' => [
                        'id' => $room->floor?->id,
                        'name' => $room->floor?->name,
                        'number' => $room->floor?->number,
                    ],
                ];
            });

        return response()->json(['data' => $availableRooms]);
    }
}
