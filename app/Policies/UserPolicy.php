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
        // User can create new users only if they have permission and their level is higher than or equal to the new user's role level
        return $user->hasPermission('create-user');
    }

    /**
     * Determine whether the user can update the user.
     */
    public function update(User $user, User $model)
    {
        // Prevent updates if the target user has a higher role level
        return ($user->hasPermission('update-user') && $user->role->level >= $model->role->level) || $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the user.
     */
    public function delete(User $user, User $model)
    {
        // Prevent users from deleting themselves or deleting users with higher role levels
        return $user->hasPermission('delete-user') && $user->id !== $model->id && $user->role->level >= $model->role->level;
    }

    /**
     * Determine whether the user can assign roles.
     */
    public function assignRole(User $user, User $model)
    {
        // Only allow assigning roles with a level lower or equal to the current user's role level
        return $user->hasPermission('assign-role') && $user->role->level >= $model->role->level;
    }

    /**
     * Determine whether the user can assign permissions.
     */
    public function assignPermissions(User $user, User $model)
    {
        // Only allow assigning permissions if user has permission and they are not assigning permissions to a higher-level user
        return $user->hasPermission('assign-permissions') && $user->role->level >= $model->role->level;
    }
}
