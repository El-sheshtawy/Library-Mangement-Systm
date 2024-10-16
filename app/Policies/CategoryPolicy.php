<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CategoryGroup;

class CategoryPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasPermission('view-categories');
    }

    public function view(User $user, CategoryGroup $categoryGroup)
    {
        return $user->hasPermission('view-categories');
    }

    public function create(User $user)
    {
        return $user->hasPermission('create-category');
    }

    public function update(User $user, CategoryGroup $categoryGroup)
    {
        return $user->hasPermission('edit-category');
    }

    public function delete(User $user, CategoryGroup $categoryGroup)
    {
        return $user->hasPermission('delete-category');
    }
}
