<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Admin\UserManagementController;

class ReceptionistController extends UserManagementController
{
    protected string $role = 'receptionist';

    protected string $routePrefix = 'manager.receptionists';

    protected string $pageDirectory = 'Manager/Receptionists';

    protected string $collectionKey = 'receptionists';

    protected string $singularKey = 'receptionist';

    protected string $resourceLabel = 'Receptionist';
}
