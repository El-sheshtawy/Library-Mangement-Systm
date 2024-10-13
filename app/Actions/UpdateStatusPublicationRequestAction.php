<?php

namespace App\Actions;

use App\Models\PublicationRequest;
use App\Notifications\PublicationRequestStatusNotification;

class UpdateStatusPublicationRequestAction
{
    public function updateStatus(PublicationRequest $publicationRequest, $status)
    {
        // Update the status of the publication request
        $publicationRequest->update(['status' => $status]);

        // Send notification to the user associated with the publication request
        $user = $publicationRequest->user;
        $user->notify(new PublicationRequestStatusNotification($publicationRequest));

        // check for a specific status to send the notification:
        if (in_array($status, ['approved', 'rejected'])) {
            $user->notify(new PublicationRequestStatusNotification($publicationRequest));
        }

        return response()->json(['message' => 'Publication request status updated.']);
    }

}
