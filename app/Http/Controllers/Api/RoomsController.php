<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rooms = Room::query()
            ->with('floor:id,name,number')
            ->orderBy('number')
            ->paginate(10)
            ->through(fn (Room $room) => [
                'id' => $room->id,
                'number' => $room->number,
                'capacity' => $room->capacity,
                'price' => $room->price,
                'price_dollars' => number_format($room->price / 100, 2, '.', ''),
                'floor_name' => $room->floor?->name,
                'floor_number' => $room->floor?->number,
            ]);

        return response()->json($rooms);
    }
}
