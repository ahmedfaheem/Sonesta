<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Rinvex\Country\Country;

class ManagerController extends UserManagementController
{
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
            'countries' => $this->countries(),
        ]);
    }

    public function edit(User $user): Response
    {
        $user = $this->ensureUserMatchesRole($user);
        $this->authorizeUpdateUser($user);

        return Inertia::render($this->page('Edit'), [
            $this->singularKey => $this->serializeUser($user),
            'countries' => $this->countries(),
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

    protected function countries(): array
    {
        return Cache::remember('countries.v2', 86400, function () {
            return collect(countries())
                ->map(fn (array|Country $country) => [
                    'name' => is_array($country) ? data_get($country, 'name.common', data_get($country, 'name')) : $country->getName(),
                ])
                ->filter(fn (array $country) => filled($country['name']))
                ->sortBy('name')
                ->values()
                ->all();
        });
    }
}
