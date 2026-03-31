<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Admin\UserManagementController;

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
}

