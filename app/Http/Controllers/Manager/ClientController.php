<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Admin\UserManagementController;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use Rinvex\Country\Country;

class ClientController extends UserManagementController
{
    protected string $role = 'client';

    protected string $routePrefix = 'manager.clients';

    protected string $pageDirectory = 'Manager/Clients';

    protected string $collectionKey = 'clients';

    protected string $singularKey = 'client';

    protected string $resourceLabel = 'Client';

    protected bool $supportsApproval = true;

    protected function hasDedicatedShowPage(): bool
    {
        return true;
    }

    public function create(): Response
    {
        $this->authorizeCreate();

        return Inertia::render($this->page('Create'), [
            'countries' => Cache::remember('countries', 86400, function () {
                return collect(countries())
                    ->map(fn (array|Country $country) => [
                        'name' => is_array($country) ? data_get($country, 'name.common', data_get($country, 'name')) : $country->getName(),
                    ])
                    ->filter(fn (array $country) => filled($country['name']))
                    ->sortBy('name')
                    ->values();
            }),
        ]);
    }

    public function edit(User $user): Response
    {
        $user = $this->ensureUserMatchesRole($user);
        $this->authorizeUpdateUser($user);

        return Inertia::render($this->page('Edit'), [
            $this->singularKey => $this->serializeUser($user),
            'countries' => Cache::remember('countries', 86400, function () {
                return collect(countries())
                    ->map(fn (array|Country $country) => [
                        'name' => is_array($country) ? data_get($country, 'name.common', data_get($country, 'name')) : $country->getName(),
                    ])
                    ->filter(fn (array $country) => filled($country['name']))
                    ->sortBy('name')
                    ->values();
            }),
        ]);
    }
}
