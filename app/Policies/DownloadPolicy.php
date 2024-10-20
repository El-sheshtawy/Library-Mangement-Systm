<?php

namespace App\Policies;

use App\Models\User;

class DownloadPolicy
{
    public function view(User $user)
    {
        return $user->hasRole('user');  // Only users with the 'user' role can view
    }
}
