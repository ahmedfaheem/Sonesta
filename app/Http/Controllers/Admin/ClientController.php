<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClientsExport;

class ClientController extends UserManagementController
{
    protected string $role = 'client';

    protected string $routePrefix = 'admin.clients';

    protected string $pageDirectory = 'Admin/Clients';

    protected string $collectionKey = 'clients';

    protected string $singularKey = 'client';

    protected string $resourceLabel = 'Client';

    protected bool $supportsApproval = true;

    protected function hasDedicatedShowPage(): bool
    {
        return true;
    }

    public function export()
    {
        return (new ClientsExport())->download('clients-'.now()->format('YmdHis').'.xlsx');
    }
}
