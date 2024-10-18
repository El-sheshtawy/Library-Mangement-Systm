<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\PublicationRequestResource;
use App\Models\PublicationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PublicationRequestController extends Controller
{
    // For the index method:
    public function index()
    {
        $this->authorize('viewAny', PublicationRequest::class);

        $status = request('status'); // Optional filter by status (pending, approved, rejected)
        $paginationSize = request('per_page', 10); // Allow per_page parameter

        $requestsQuery = PublicationRequest::with(['book', 'user'])
            ->when($status, fn($query) => $query->where('status', $status));

        $publicationRequests = $requestsQuery->paginate($paginationSize);

        return PublicationRequestResource::collection($publicationRequests);
    }

    // For the show method:
    public function show($id)
    {
        $publicationRequest = PublicationRequest::with(['book', 'user'])->findOrFail($id);
        $this->authorize('view', $publicationRequest);

        // Conditionally eager load media if needed
        if (request()->has('include_media')) {
            $publicationRequest->load('media');
        }

        return new PublicationRequestResource($publicationRequest);
    }

    // For the store method:
    public function store(Request $request)
    {
        $this->authorize('create', PublicationRequest::class);

        // Validate the request data
        $validatedData = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'publisher_name' => ['required', 'string', 'max:255'],
            'copyright_image' => ['required', 'file', 'image', 'mimes:jpg,png,jpeg', 'max:5120'], // Max 5MB
            'book_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'], // Max 20MB
        ]);

        // Create a new publication request
        $publicationRequest = PublicationRequest::create(array_merge(
            $validatedData,
            ['user_id' => Auth::id(), 'status' => 'pending']
        ));

        // Handle file uploads (DRY optimization)
        $mediaCollections = [
            'copyright_image' => 'copyright_image',
            'book_file' => 'book_file',
        ];

        foreach ($mediaCollections as $field => $collection) {
            if ($request->hasFile($field)) {
                $publicationRequest->addMedia($request->file($field))->toMediaCollection($collection);
            }
        }

        return new PublicationRequestResource($publicationRequest);
    }

    // Common logic for approving/rejecting requests
    private function updateRequestStatus(PublicationRequest $publicationRequest, string $status, string $reason = null)
    {
        $this->authorize('update', $publicationRequest);

        if ($publicationRequest->status !== 'pending') {
            return response()->json(['message' => 'The request has already been processed.'], 400);
        }

        $data = ['status' => $status];
        if ($status === 'rejected') {
            $data['rejection_reason'] = $reason ?? 'No reason provided';
        }

        $publicationRequest->update($data);

        if ($status === 'approved') {
            $publicationRequest->book->update(['is_approved' => true]);
        }

        return new PublicationRequestResource($publicationRequest);
    }

    // For the approve method:
    public function approve($id)
    {
        $publicationRequest = PublicationRequest::findOrFail($id);
        return $this->updateRequestStatus($publicationRequest, 'approved');
    }

    // For the reject method:
    public function reject(Request $request, $id)
    {
        $publicationRequest = PublicationRequest::findOrFail($id);
        return $this->updateRequestStatus($publicationRequest, 'rejected', $request->input('rejection_reason'));
    }

    // For the destroy method:
    public function destroy($id)
    {
        $publicationRequest = PublicationRequest::findOrFail($id);
        $this->authorize('delete', $publicationRequest);

        if ($publicationRequest->status !== 'pending') {
            return response()->json(['message' => 'Only pending requests can be deleted.'], 400);
        }

        $publicationRequest->delete();

        return response()->json(['message' => 'The publication request has been deleted.'], 200);
    }
}
