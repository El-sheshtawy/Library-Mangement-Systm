<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('view-users');
    }

    /**
     * Determine whether the user can view the user.
     */
    public function view(User $user, User $model)
    {
        return $user->hasPermission('view-users') || $user->id === $model->id;
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user)
    {
        return $user->hasPermission('create-user');
    }

    /**
     * Determine whether the user can update the user.
     */
    public function update(User $user, User $model)
    {
        return $user->hasPermission('update-user') || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the user.
     */
    public function delete(User $user, User $model)
    {
        // Prevent users from deleting themselves
        return $user->hasPermission('delete-user') && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can assign roles.
     */
    public function assignRole(User $user, User $model)
    {
        return $user->hasPermission('assign-role');
    }

    /**
     * Determine whether the user can assign permissions.
     */
    public function assignPermissions(User $user, User $model)
    {
        return $user->hasPermission('assign-permissions');
    }
}
