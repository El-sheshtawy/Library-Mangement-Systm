<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('view-roles');
    }

    public function view(User $user, Role $role)
    {
        return $user->hasPermission('view-roles');
    }

    public function create(User $user)
    {
        return $user->hasPermission('create-role');
    }

    public function update(User $user, Role $role)
    {
        return $user->hasPermission('update-role');
    }

    public function delete(User $user, Role $role)
    {
        return $user->hasPermission('delete-role');
    }

    public function assignPermissions(User $user, Role $role)
    {
        return $user->hasPermission('assign-permissions');
    }
}
