<?php

namespace App\Policies;

use App\Models\BookSeries;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookSeriesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the book series.
     */
    public function update(User $user, BookSeries $bookSeries)
    {
        return $user->id === $bookSeries->user_id || $user->hasRole('superAdmin') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the book series.
     */
    public function delete(User $user, BookSeries $bookSeries)
    {
        return $user->id === $bookSeries->user_id || $user->hasRole('superAdmin') || $user->hasRole('admin');
    }
}
