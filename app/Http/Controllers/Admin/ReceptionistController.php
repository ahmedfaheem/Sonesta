<?php

namespace App\Http\Controllers\Admin;

class ReceptionistController extends UserManagementController
{
    protected string $role = 'receptionist';

    protected string $routePrefix = 'admin.receptionists';

    protected string $pageDirectory = 'Admin/Receptionists';

    protected string $collectionKey = 'receptionists';

    protected string $singularKey = 'receptionist';

    protected string $resourceLabel = 'Receptionist';
}
