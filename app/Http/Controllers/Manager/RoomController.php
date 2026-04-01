<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Models\Floor;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Room::class);

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'floor_id' => $request->filled('floor_id') ? (int) $request->query('floor_id') : null,
            'sort' => $this->resolveSort((string) $request->query('sort', 'created_at')),
            'direction' => $this->resolveDirection((string) $request->query('direction', 'desc')),
        ];

        $rooms = Room::query()
            ->with(['floor:id,name,number', 'manager:id,name'])
            ->withCount('reservations')
            ->visibleTo(auth()->user())
            ->when($filters['search'] !== '', function (Builder $query) use ($filters): void {
                $search = $filters['search'];

                $query->where(function (Builder $innerQuery) use ($search): void {
                    $innerQuery
                        ->where('number', 'like', "%{$search}%")
                        ->orWhereHas('floor', function (Builder $floorQuery) use ($search): void {
                            $floorQuery
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('number', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['floor_id'], fn (Builder $query, int $floorId) => $query->where('floor_id', $floorId))
            ->orderBy($filters['sort'], $filters['direction'])
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Room $room) => $this->serializeRoom($room));

        return Inertia::render('Manager/Rooms/Index', [
            'rooms' => $rooms,
            'filters' => $filters,
            'floors' => $this->floorOptions($request),
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Room::class);

        return Inertia::render('Manager/Rooms/Create', [
            'floors' => $this->floorOptions($request),
        ]);
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        $this->authorize('create', Room::class);

        $floor = Floor::query()
            ->visibleTo(auth()->user())
            ->findOrFail($request->integer('floor_id'));

        Room::query()->create([
            'number' => $request->string('number')->toString(),
            'capacity' => $request->integer('capacity'),
            'price' => $this->priceToCents($request->input('price')),
            'floor_id' => $floor->id,
            'manager_id' => $floor->manager_id,
        ]);

        return redirect()
            ->route($this->indexRouteName($request))
            ->with('success', 'Room created successfully.');
    }

    public function edit(Request $request, Room $room): Response
    {
        $this->authorize('update', $room);

        return Inertia::render('Manager/Rooms/Edit', [
            'room' => $this->serializeRoom($room->load(['floor:id,name,number,manager_id', 'manager:id,name'])),
            'floors' => $this->floorOptions($request),
        ]);
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $this->authorize('update', $room);

        $floor = Floor::query()
            ->visibleTo(auth()->user())
            ->findOrFail($request->integer('floor_id'));

        $room->update([
            'number' => $request->string('number')->toString(),
            'capacity' => $request->integer('capacity'),
            'price' => $this->priceToCents($request->input('price')),
            'floor_id' => $floor->id,
            'manager_id' => $floor->manager_id,
        ]);

        return redirect()
            ->route($this->indexRouteName($request))
            ->with('success', 'Room updated successfully.');
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function destroy(Room $room): RedirectResponse
    {
        $this->authorize('delete', $room);

        if ($room->reservations()->exists()) {
            throw ValidationException::withMessages([
                'room' => 'This room cannot be deleted because it has reservations.',
            ]);
        }

        $room->delete();

        return back()->with('success', 'Room deleted successfully.');
    }

    protected function priceToCents(mixed $price): int
    {
        return (int) round(((float) $price) * 100);
    }

    protected function serializeRoom(Room $room): array
    {
        return [
            'id' => $room->id,
            'number' => $room->number,
            'capacity' => $room->capacity,
            'price' => $room->price,
            'price_dollars' => number_format($room->price / 100, 2, '.', ''),
            'floor_id' => $room->floor_id,
            'floor_name' => $room->floor?->name,
            'floor_number' => $room->floor?->number,
            'manager_id' => $room->manager_id,
            'manager_name' => $room->manager?->name,
            'reservations_count' => $room->reservations_count ?? $room->reservations()->count(),
            'created_at' => $room->created_at?->toDateTimeString(),
        ];
    }

    protected function resolveSort(string $sort): string
    {
        return in_array($sort, ['number', 'capacity', 'price', 'created_at'], true) ? $sort : 'created_at';
    }

    protected function resolveDirection(string $direction): string
    {
        return $direction === 'asc' ? 'asc' : 'desc';
    }

    protected function floorOptions(Request $request): array
    {
        return Floor::query()
            ->visibleTo(auth()->user())
            ->orderBy('name')
            ->get(['id', 'name', 'number', 'manager_id'])
            ->map(fn (Floor $floor) => [
                'id' => $floor->id,
                'name' => $floor->name,
                'number' => $floor->number,
                'manager_id' => $floor->manager_id,
            ])
            ->all();
    }

    protected function indexRouteName(Request $request): string
    {
        return $request->user()->hasRole('admin') ? 'admin.rooms.index' : 'manager.rooms.index';
    }
}
