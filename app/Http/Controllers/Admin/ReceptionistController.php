<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Services\CountryService;
use Inertia\Inertia;
use Inertia\Response;

class ReceptionistController extends UserManagementController
{
    public function __construct(private readonly CountryService $countryService)
    {
    }

    protected string $role = 'receptionist';

    protected string $routePrefix = 'admin.receptionists';

    protected string $pageDirectory = 'Admin/Receptionists';

    protected string $collectionKey = 'receptionists';

    protected string $singularKey = 'receptionist';

    protected string $resourceLabel = 'Receptionist';

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

}
