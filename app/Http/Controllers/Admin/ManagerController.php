<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class ManagerController extends UserManagementController
{
    protected string $role = 'manager';

    protected string $routePrefix = 'admin.managers';

    protected string $pageDirectory = 'Admin/Managers';

    protected string $collectionKey = 'managers';

    protected string $singularKey = 'manager';

    protected string $resourceLabel = 'Manager';

    protected bool $supportsApproval = true;

    public function toggleBan(User $user): RedirectResponse
    {
        $user = $this->ensureUserMatchesRole($user);

        $isCurrentlyBanned = $this->isManagerBanned($user);

        $user->forceFill([
            'is_approved' => $isCurrentlyBanned,
            'approved_by' => $isCurrentlyBanned ? null : auth()->id(),
        ])->save();

        return back()->with(
            'success',
            $isCurrentlyBanned
                ? "{$user->name} has been unbanned."
                : "{$user->name} has been banned."
        );
    }
}
