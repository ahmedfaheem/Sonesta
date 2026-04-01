<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Admin\UserManagementController;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use Rinvex\Country\Country;

class ReceptionistController extends UserManagementController
{
    protected string $role = 'receptionist';

    protected string $routePrefix = 'manager.receptionists';

    protected string $pageDirectory = 'Manager/Receptionists';

    protected string $collectionKey = 'receptionists';

    protected string $singularKey = 'receptionist';

    protected string $resourceLabel = 'Receptionist';

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

    protected function canManageUser(User $user): bool
    {
        $authUser = auth()->user();

        if (! $authUser) {
            return false;
        }

        return (int) $user->created_by === (int) $authUser->id;
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
