<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFloorRequest;
use App\Http\Requests\UpdateFloorRequest;
use App\Models\Floor;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class FloorController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Floor::class);

        $floors = QueryBuilder::for(
            Floor::query()
            ->with('manager:id,name')
            ->withCount('rooms')
            ->visibleTo(auth()->user())
        )
            ->allowedFilters(
                AllowedFilter::callback('search', function (Builder $query, string $value): void {
                    $search = trim($value);

                    if ($search === '') {
                        return;
                    }

                    $query->where(function (Builder $innerQuery) use ($search): void {
                        $innerQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('number', 'like', "%{$search}%");
                    });
                }),
            )
            ->allowedSorts('name', 'number', 'created_at')
            ->defaultSort('-created_at')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString()
            ->through(fn (Floor $floor) => $this->serializeFloor($floor));

        return Inertia::render('Manager/Floors/Index', [
            'floors' => $floors,
            'query' => [
                'filter' => [
                    'search' => $request->input('filter.search', ''),
                ],
                'sort' => $request->input('sort', '-created_at'),
                'page' => $request->integer('page', 1),
                'per_page' => $request->integer('per_page', 10),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Floor::class);

        return Inertia::render('Manager/Floors/Create', [
            'managers' => $this->managerOptions($request->user()),
        ]);
    }

    public function store(StoreFloorRequest $request): RedirectResponse
    {
        $this->authorize('create', Floor::class);

        $managerId = $request->user()->hasRole('admin')
            ? (int) $request->integer('manager_id')
            : $request->user()->id;

        Floor::query()->create([
            'name' => $request->string('name')->toString(),
            'number' => Floor::generateUniqueNumber(),
            'manager_id' => $managerId,
        ]);

        return redirect()
            ->route($this->indexRouteName($request))
            ->with('success', 'Floor created successfully.');
    }

    public function edit(Request $request, Floor $floor): Response
    {
        $this->authorize('update', $floor);

        return Inertia::render('Manager/Floors/Edit', [
            'floor' => $this->serializeFloor($floor->load('manager:id,name')),
            'managers' => $this->managerOptions($request->user()),
        ]);
    }

    public function update(UpdateFloorRequest $request, Floor $floor): RedirectResponse
    {
        $this->authorize('update', $floor);

        $managerId = $request->user()->hasRole('admin')
            ? (int) $request->integer('manager_id')
            : $floor->manager_id;

        $floor->update([
            'name' => $request->string('name')->toString(),
            'manager_id' => $managerId,
        ]);

        $floor->rooms()->update([
            'manager_id' => $managerId,
        ]);

        return redirect()
            ->route($this->indexRouteName($request))
            ->with('success', 'Floor updated successfully.');
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function destroy(Floor $floor): RedirectResponse
    {
        $this->authorize('delete', $floor);

        if ($floor->rooms()->exists()) {
            throw ValidationException::withMessages([
                'floor' => 'This floor cannot be deleted because it still has rooms.',
            ]);
        }

        $floor->delete();

        return back()->with('success', 'Floor deleted successfully.');
    }

    /**
     * @return array<int, array{id:int,name:string}>
     */
    protected function managerOptions(User $user): array
    {
        if ($user->hasRole('admin')) {
            return User::query()
                ->role('manager')
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (User $manager) => [
                    'id' => $manager->id,
                    'name' => $manager->name,
                ])
                ->all();
        }

        return [[
            'id' => $user->id,
            'name' => $user->name,
        ]];
    }

    protected function serializeFloor(Floor $floor): array
    {
        return [
            'id' => $floor->id,
            'name' => $floor->name,
            'number' => $floor->number,
            'manager_id' => $floor->manager_id,
            'manager_name' => $floor->manager?->name,
            'rooms_count' => $floor->rooms_count ?? $floor->rooms()->count(),
            'created_at' => $floor->created_at?->toDateTimeString(),
        ];
    }

    protected function indexRouteName(Request $request): string
    {
        return $request->user()->hasRole('admin') ? 'admin.floors.index' : 'manager.floors.index';
    }
}
