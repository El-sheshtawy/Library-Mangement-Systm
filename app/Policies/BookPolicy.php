<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view-books');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Book $book): bool
    {
        return $user->hasPermission('view-books');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create-books');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Book $book): bool
    {
        // Check if the user has the 'update-books' permission and:
        // - The user is the owner of the book, or
        // - The user has a role that allows them to edit any book (e.g., super_admin)
        return $user->hasPermission('update-books') && ($user->id === $book->user_id || !$user->hasRole('user'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Book $book): bool
    {
        // Check if the user has the 'delete-books' permission and:
        // - The user is the owner of the book, or
        // - The user has a role that allows them to delete any book (e.g., super_admin)
        return $user->hasPermission('delete-books') && ($user->id === $book->user_id || !$user->hasRole('user'));
    }
}
