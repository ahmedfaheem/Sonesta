<?php

namespace App\Http\Controllers\Admin;

class ManagerController extends UserManagementController
{
    protected string $role = 'manager';

    protected string $routePrefix = 'admin.managers';

    protected string $pageDirectory = 'Admin/Managers';

    protected string $collectionKey = 'managers';

    protected string $singularKey = 'manager';

    protected string $resourceLabel = 'Manager';
}
