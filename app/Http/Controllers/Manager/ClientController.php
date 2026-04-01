<?php

namespace App\Http\Controllers\Manager;

use App\Exports\ManagerClientsExport;
use App\Http\Controllers\Admin\UserManagementController;
use App\Models\User;
use App\Services\CountryService;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends UserManagementController
{
    public function __construct(private readonly CountryService $countryService)
    {
    }

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

    public function export()
    {
        $this->authorizeIndex();

        return (new ManagerClientsExport(auth()->id()))
            ->download('manager-clients-'.now()->format('YmdHis').'.xlsx');
    }
}
