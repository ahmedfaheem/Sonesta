<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Services\CountryService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ManagerController extends UserManagementController
{
    public function __construct(private readonly CountryService $countryService)
    {
    }

    protected string $role = 'manager';

    protected string $routePrefix = 'admin.managers';

    protected string $pageDirectory = 'Admin/Managers';

    protected string $collectionKey = 'managers';

    protected string $singularKey = 'manager';

    protected string $resourceLabel = 'Manager';

    protected bool $supportsApproval = false;

    public function create(): Response
    {
        $this->authorizeCreate();

        return Inertia::render($this->page('Create'), [
            'countries' => $this->countryService->all(),
        ]);
    }

    public function edit(User $user): Response
    {
        $user = $this->ensureUserMatchesRole($user);
        $this->authorizeUpdateUser($user);

        return Inertia::render($this->page('Edit'), [
            $this->singularKey => $this->serializeUser($user),
            'countries' => $this->countryService->all(),
        ]);
    }

    public function toggleBan(User $user): RedirectResponse
    {
        $user = $this->ensureUserMatchesRole($user);

        $isCurrentlyBanned = (bool) $user->is_banned;

        $user->forceFill([
            'is_banned' => ! $isCurrentlyBanned,
        ])->save();

        return back()->with(
            'success',
            $isCurrentlyBanned
                ? "{$user->name} has been unbanned."
                : "{$user->name} has been banned."
        );
    }

}
