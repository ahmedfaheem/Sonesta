<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Client;
use App\Models\User;
use App\Notifications\ClientApprovedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

abstract class UserManagementController extends Controller
{
    protected string $role;

    protected string $routePrefix;

    protected string $pageDirectory;

    protected string $collectionKey;

    protected string $singularKey;

    protected string $resourceLabel;

    protected bool $supportsApproval = false;

    public function index(Request $request): Response
    {
        $this->authorizeIndex();

        $users = QueryBuilder::for($this->usersQuery())
            ->with('createdBy')
            ->allowedFilters(
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
                AllowedFilter::exact('is_approved')
            )
            ->allowedSorts(
                'name',
                'email',
                'created_at'
            )
            ->defaultSort('-created_at')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString()
            ->through(fn (User $user) => $this->serializeUser($user));

        return Inertia::render($this->page('Index'), [
            $this->collectionKey => $users,
        ]);
    }

    public function create(): Response
    {
        $this->authorizeCreate();

        return Inertia::render($this->page('Create'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->authorizeStore();

        $user = $this->saveUser(new User, $request);
        $user->assignRole($this->role);

        return redirect()->route($this->route('index'))
            ->with('success', "{$this->resourceLabel} created");
    }

    public function show(User $user): Response|RedirectResponse
    {
        $user = $this->ensureUserMatchesRole($user);

        if ($this->hasDedicatedShowPage()) {
            return Inertia::render($this->page('Show'), [
                $this->singularKey => $this->serializeUser($user),
            ]);
        }

        return redirect()->route($this->route('edit'), $user);
    }

    public function edit(User $user): Response
    {
        $user = $this->ensureUserMatchesRole($user);
        $this->authorizeUpdateUser($user);

        return Inertia::render($this->page('Edit'), [
            $this->singularKey => $this->serializeUser($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user = $this->ensureUserMatchesRole($user);
        $this->authorizeUpdateUser($user);

        $this->saveUser($user, $request);

        return redirect()->route($this->route('index'))
            ->with('success', "{$this->resourceLabel} updated");
    }

    public function destroy(User $user): RedirectResponse
    {
        $user = $this->ensureUserMatchesRole($user);
        $this->authorizeDeleteUser($user);

        if ($this->role === 'manager') {
            $hasFloors = $user->floors()->exists();
            $hasRooms = $user->rooms()->exists();
            $hasReceptionists = $user->createdUsers()
                ->whereHas('roles', fn (Builder $query) => $query->where('name', 'receptionist'))
                ->exists();

            if ($hasFloors || $hasRooms || $hasReceptionists) {
                return back()->with('error', 'Manager cannot be deleted while assigned to floors, rooms, or receptionists.');
            }
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return back()->with('success', 'Deleted');
    }

    public function toggleBan(User $user): RedirectResponse
    {
        $user = $this->ensureUserMatchesRole($user);

        abort_unless($this->role === 'receptionist', HttpResponse::HTTP_NOT_FOUND);

        if (auth()->user()?->hasRole('manager')) {
            $this->authorize('banReceptionist', $user);
        }

        $isCurrentlyBanned = ! (bool) $user->is_approved;

        $user->forceFill([
            'is_approved' => $isCurrentlyBanned,
            'approved_by' => $isCurrentlyBanned ? null : auth()->id(),
        ])->save();

        return back()->with(
            'success',
            $isCurrentlyBanned
                ? "{$user->name} has been unbanned."
                : "{$user->name} has been banned."
        );
    }

    protected function usersQuery(): Builder
    {
        return User::query()
            ->with('createdBy')
            ->role($this->role);
    }

    protected function saveUser(User $user, StoreUserRequest|UpdateUserRequest $request): User
    {
        $validated = $request->validated();

        $user->name = $validated['name'] ?? $user->name;
        $user->email = $validated['email'] ?? $user->email;
        $user->national_id = $validated['national_id'] ?? null;
        $user->created_by = $user->exists ? $user->created_by : $request->user()->id;

        $wasPreviouslyApproved = $user->exists ? (bool) $user->is_approved : true;

        if ($this->supportsApproval) {
            $defaultApproval = $user->exists ? (bool) $user->is_approved : true;
            $user->is_approved = (bool) ($validated['is_approved'] ?? $defaultApproval);
        } elseif (! $user->exists) {
            // Non-client managed users should be active by default.
            $user->is_approved = true;
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            $user->avatar = $this->storeAvatar($request->file('avatar'), $user->avatar);
        }

        $user->save();

        if ($this->role === 'client') {
            $clientProfile = $user->clientProfile;

            if ($clientProfile) {
                $clientProfile->update([
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $validated['phone'] ?? $clientProfile->phone,
                    'country' => $validated['country'] ?? $clientProfile->country,
                    'gender' => $validated['gender'] ?? $clientProfile->gender,
                    'is_approved' => $user->is_approved,
                    'approved_by' => $user->is_approved ? ($clientProfile->approved_by ?? auth()->id()) : null,
                ]);
            } else {
                Client::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $validated['phone'] ?? null,
                    'country' => $validated['country'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'avatar' => $user->avatar,
                    'is_approved' => $user->is_approved,
                    'approved_by' => $user->is_approved ? auth()->id() : null,
                ]);
            }

            if ($user->is_approved && ! $wasPreviouslyApproved) {
                $user->notify(new ClientApprovedNotification);
            }
        }

        return $user->fresh('createdBy');
    }

    protected function storeAvatar(UploadedFile $avatar, ?string $currentAvatar = null): string
    {
        if ($currentAvatar) {
            Storage::disk('public')->delete($currentAvatar);
        }

        return Storage::disk('public')->putFile('avatars', $avatar);
    }

    protected function ensureUserMatchesRole(User $user): User
    {
        abort_unless($user->hasRole($this->role), HttpResponse::HTTP_NOT_FOUND);

        if (
            $this->role === 'receptionist'
            && auth()->user()?->hasRole('manager')
            && (int) $user->created_by !== (int) auth()->id()
        ) {
            abort(HttpResponse::HTTP_FORBIDDEN);
        }

        return $user;
    }

    protected function serializeUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'national_id' => $user->national_id,
            'phone' => $this->role === 'client' ? $user->clientProfile?->phone : null,
            'country' => $this->role === 'client' ? $user->clientProfile?->country : null,
            'gender' => $this->role === 'client' ? $user->clientProfile?->gender : null,
            'avatar' => $user->avatar,
            'avatar_url' => $this->avatarUrl($user->avatar),
            'created_at' => $user->created_at?->toISOString(),
            'created_by' => $user->created_by,
            'created_by_name' => $user->createdBy?->name,
            'is_approved' => $this->isApproved($user),
        ];
    }

    protected function isApproved(User $user): bool
    {
        if ($this->role === 'manager') {
            return ! $this->isManagerBanned($user);
        }

        return (bool) $user->is_approved;
    }

    protected function isManagerBanned(User $user): bool
    {
        $rawApproval = $user->getRawOriginal('is_approved');

        return (int) $rawApproval === 0 && ! is_null($user->approved_by);
    }

    protected function avatarUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : asset('storage/'.ltrim($path, '/'));
    }

    protected function page(string $name): string
    {
        return "{$this->pageDirectory}/{$name}";
    }

    protected function route(string $name): string
    {
        return "{$this->routePrefix}.{$name}";
    }

    protected function hasDedicatedShowPage(): bool
    {
        return false;
    }

    protected function authorizeIndex(): void
    {
        if ($this->role === 'receptionist' && auth()->user()?->hasRole('manager')) {
            $this->authorize('viewAnyReceptionists', User::class);
        }

        if ($this->role === 'client' && auth()->user()?->hasRole('manager')) {
            $this->authorize('viewAnyClients', User::class);
        }
    }

    protected function authorizeCreate(): void
    {
        if ($this->role === 'receptionist' && auth()->user()?->hasRole('manager')) {
            $this->authorize('createReceptionist', User::class);
        }

        if ($this->role === 'client' && auth()->user()?->hasRole('manager')) {
            $this->authorize('createClient', User::class);
        }
    }

    protected function authorizeStore(): void
    {
        $this->authorizeCreate();
    }

    protected function authorizeUpdateUser(User $user): void
    {
        if ($this->role === 'receptionist' && auth()->user()?->hasRole('manager')) {
            $this->authorize('updateReceptionist', $user);
        }

        if ($this->role === 'client' && auth()->user()?->hasRole('manager')) {
            $this->authorize('updateClient', $user);
        }
    }

    protected function authorizeDeleteUser(User $user): void
    {
        if ($this->role === 'receptionist' && auth()->user()?->hasRole('manager')) {
            $this->authorize('deleteReceptionist', $user);
        }

        if ($this->role === 'client' && auth()->user()?->hasRole('manager')) {
            $this->authorize('deleteClient', $user);
        }
    }
}
