<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any permissions.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('view-permissions');
    }

    /**
     * Determine whether the user can view the permission.
     */
    public function view(User $user, Permission $permission)
    {
        return $user->hasPermission('view-permissions');
    }

    /**
     * Determine whether the user can create permissions.
     */
    public function create(User $user)
    {
        return $user->hasPermission('create-permission');
    }

    /**
     * Determine whether the user can update the permission.
     */
    public function update(User $user, Permission $permission)
    {
        return $user->hasPermission('update-permission');
    }

    /**
     * Determine whether the user can delete the permission.
     */
    public function delete(User $user, Permission $permission)
    {
        return $user->hasPermission('delete-permission');
    }
}
