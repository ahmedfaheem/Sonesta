<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
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

    public function index(): Response
    {
        return Inertia::render($this->page('Index'), [
            $this->collectionKey => $this->usersQuery()
                ->latest()
                ->paginate(10)
                ->through(fn (User $user) => $this->serializeUser($user)),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render($this->page('Create'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
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

        return Inertia::render($this->page('Edit'), [
            $this->singularKey => $this->serializeUser($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user = $this->ensureUserMatchesRole($user);

        $this->saveUser($user, $request);

        return redirect()->route($this->route('index'))
            ->with('success', "{$this->resourceLabel} updated");
    }

    public function destroy(User $user): RedirectResponse
    {
        $user = $this->ensureUserMatchesRole($user);

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return back()->with('success', 'Deleted');
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

        if ($this->supportsApproval) {
            $user->is_approved = (bool) ($validated['is_approved'] ?? $user->is_approved ?? false);
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            $user->avatar = $this->storeAvatar($request->file('avatar'), $user->avatar);
        }

        $user->save();

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

        return $user;
    }

    protected function serializeUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'national_id' => $user->national_id,
            'avatar' => $user->avatar,
            'avatar_url' => $user->avatar ? asset('storage/'.$user->avatar) : null,
            'created_at' => $user->created_at?->toISOString(),
            'created_by' => $user->created_by,
            'created_by_name' => $user->createdBy?->name,
            'is_approved' => (bool) $user->is_approved,
        ];
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
}
