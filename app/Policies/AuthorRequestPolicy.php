<?php

namespace App\Policies;

use App\Models\AuthorRequest;
use App\Models\User;

class AuthorRequestPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasRole('admin') || $user->hasRole('superAdmin');
    }

    public function view(User $user, AuthorRequest $authorRequest)
    {
        return $user->hasRole('admin') || $user->hasRole('superAdmin');
    }

    public function update(User $user, AuthorRequest $authorRequest)
    {
        return $user->id === $authorRequest->user_id || $user->hasRole('admin') || $user->hasRole('superAdmin');
    }

    public function delete(User $user, AuthorRequest $authorRequest)
    {
        return $user->hasRole('admin') || $user->hasRole('superAdmin');
    }

    public function handleRequest(User $user)
    {
        return $user->hasRole('admin') || $user->hasRole('superAdmin');
    }
}
