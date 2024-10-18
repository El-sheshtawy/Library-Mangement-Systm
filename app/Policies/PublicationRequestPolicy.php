<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PublicationRequest;

class PublicationRequestPolicy
{
    /**
     * Determine whether the user can view any publication requests.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('view_publication_requests');
    }

    /**
     * Determine whether the user can view a specific publication request.
     */
    public function view(User $user, PublicationRequest $publicationRequest)
    {
        return $user->hasPermission('view_publication_requests') || $user->id === $publicationRequest->user_id;
    }

    /**
     * Determine whether the user can create publication requests.
     */
    public function create(User $user)
    {
        return $user->hasPermission('create_publication_requests');
    }

    /**
     * Determine whether the user can update the publication request.
     */
    public function update(User $user, PublicationRequest $publicationRequest)
    {
        return $user->hasPermission('update_publication_requests') && $user->id === $publicationRequest->user_id;
    }

    /**
     * Determine whether the user can delete the publication request.
     */
    public function delete(User $user, PublicationRequest $publicationRequest)
    {
        return $user->hasPermission('delete_publication_requests') && ($user->id === $publicationRequest->user_id);
    }
}
