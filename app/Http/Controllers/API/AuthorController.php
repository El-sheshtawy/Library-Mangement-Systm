<?php

namespace App\Http\Controllers\API;

use App\Constants\MediaConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\Author;
use App\Models\AuthorRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\AuthorResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Auth\Access\AuthorizationException;

class AuthorController extends Controller
{
    public function __construct()
    {
        // Simulate user login for testing purposes
        // Remove or modify this in production
        if (app()->environment('local')) {
            Auth::loginUsingId(1);
        }
        $this->authorizeResource(AuthorRequest::class, 'authorRequest');
    }

    /**
     * Submit a new author request.
     */
    public function requestAuthor(StoreAuthorRequest $request)
    {
        $validated = $request->validated();

        try {
            // Create a new author request with 'pending' status
            $authorRequest = AuthorRequest::create([
                'name' => $validated['name'],
                'biography' => $validated['biography'] ?? null,
                'birthdate' => $validated['birthdate'] ?? null,
                'user_id' => Auth::id(),
                'status' => 'pending',
            ]);

            // Handle Profile Image Upload
            $this->handleFileUpload(
                $authorRequest,
                $request,
                'profile_image',
                'profile_image',
                MediaConstants::DEFAULT_AUTHORREQUEST_IMAGE
            );

            // Handle Cover Image Upload
            $this->handleFileUpload(
                $authorRequest,
                $request,
                'cover_image',
                'cover_image',
                MediaConstants::DEFAULT_BOOK_IMAGE // Example: Replace with the correct constant if needed
            );

            // Handle Copyright Document Upload
            if ($request->hasFile('copyright')) {
                $authorRequest->addMedia($request->file('copyright'))
                    ->toMediaCollection('copyright');
            }

            return response()->json(['message' => 'Author request submitted successfully'], Response::HTTP_CREATED);

        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'Unauthorized action.',
                'details' => $e->getMessage()
            ], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to submit author request',
                'details' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Handle file upload for media collections.
     *
     * @param Model $authorRequest
     * @param Request $request
     * @param string $fileKey
     * @param string $collection
     * @param string|null $defaultPath
     */
    private function handleFileUpload($authorRequest, $request, $fileKey, $collection, $defaultPath = null)
    {
        if ($request->hasFile($fileKey)) {
            $authorRequest->addMedia($request->file($fileKey))
                ->toMediaCollection($collection);
        } elseif ($defaultPath) {
            $authorRequest->addMediaFromUrl(asset($defaultPath))
                ->toMediaCollection($collection);
        }
    }
    /**
     * List all pending author requests.
     */
    public function listRequests()
    {
        try {
            $requests = AuthorRequest::where('status', 'pending')
                ->with(['user', 'media'])
                ->get();

            return response()->json(AuthorResource::collection($requests), Response::HTTP_OK);
        } catch (AuthorizationException $e) {
            return response()->json([
                'error' => 'Unauthorized action.',
                'details' => $e->getMessage()
            ], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve author requests',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle (approve/reject) an author request.
     */
    public function handleRequest($id, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        try {
            $authorRequest = AuthorRequest::findOrFail($id);

            if ($validated['status'] === 'approved') {
                // Create the author from the request
                $author = Author::create([
                    'name' => $authorRequest->name,
                    'biography' => $authorRequest->biography,
                    'birthdate' => $authorRequest->birthdate,
                ]);

                // Transfer media collections
                $this->transferMedia($authorRequest, $author, 'profile_image');
                $this->transferMedia($authorRequest, $author, 'cover_image');
                $this->transferMedia($authorRequest, $author, 'copyright', false); // Not single collection

                // Delete the author request after successful transfer
                $authorRequest->delete();

                return response()->json(['message' => 'Author request approved and author created'], Response::HTTP_OK);
            } else {
                // Reject the author request by updating its status
                $authorRequest->update(['status' => 'rejected']);

                return response()->json(['message' => 'Author request rejected'], Response::HTTP_OK);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Author request not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to handle author request',
                'details' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Transfer media from AuthorRequest to Author.
     *
     * @param AuthorRequest $authorRequest
     * @param Author $author
     * @param string $collectionName
     * @param bool $isSingle
     */
    private function transferMedia($authorRequest, $author, $collectionName, $isSingle = true)
    {
        if ($authorRequest->hasMedia($collectionName)) {
            $authorRequest->getMedia($collectionName)->each(function (Media $media) use ($author, $collectionName, $isSingle) {
                // Copy media to the author's media collection
                $media->copy($author, $collectionName);

                // Optionally delete from the author request if it's single-use
                if ($isSingle) {
                    $media->delete();
                }
            });
        }
    }

    /**
     * Submit a request to update an existing author.
     */
    public function updateAuthorRequest(UpdateAuthorRequest $request, $id)
    {
        try {
            // Ensure the author exists
            $author = Author::findOrFail($id);

            // Create a new author update request with 'pending' status
            $authorRequest = AuthorRequest::create([
                'name' => $request->validated()['name'],
                'biography' => $request->validated()['biography'] ?? null,
                'birthdate' => $request->validated()['birthdate'] ?? null,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'author_id' => $author->id,
            ]);

            // Handle Profile Image
            if ($request->hasFile('profile_image')) {
                $authorRequest->addMedia($request->file('profile_image'))
                    ->toMediaCollection('profile_image');
            }

            // Handle Cover Image
            if ($request->hasFile('cover_image')) {
                $authorRequest->addMedia($request->file('cover_image'))
                    ->toMediaCollection('cover_image');
            }

            return response()->json(['message' => 'Author update request submitted successfully'], Response::HTTP_CREATED);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Author not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to submit author update request',
                'details' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Handle (approve/reject) an author update request.
     */
    public function handleUpdateRequest($id, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        try {
            $authorRequest = AuthorRequest::findOrFail($id);

            if ($validated['status'] === 'approved') {
                // Approve and update the author
                $this->approveAndApplyChanges($authorRequest);

                return response()->json(['message' => 'Author update approved and changes applied'], Response::HTTP_OK);
            } else {
                // Reject the author update request
                $authorRequest->update(['status' => 'rejected']);

                return response()->json(['message' => 'Author update request rejected'], Response::HTTP_OK);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Author update request not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to handle author update request',
                'details' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function approveAndApplyChanges(AuthorRequest $authorRequest)
    {
        // Ensure the author exists
        $author = Author::findOrFail($authorRequest->author_id);

        // Update the author's basic information
        $author->update([
            'name' => $authorRequest->name,
            'biography' => $authorRequest->biography,
            'birthdate' => $authorRequest->birthdate,
        ]);

        // Handle media transfer for profile and cover images
        $this->transferMedia($authorRequest, $author, 'profile_image');
        $this->transferMedia($authorRequest, $author, 'cover_image');

        // Optionally handle other media collections similarly

        // Delete the author update request after approval
        $authorRequest->delete();
    }

    /**
     * Display a specific author.
     */
    public function show($id)
    {
        try {
            $author = Author::with(['books', 'authorRequests', 'media'])->findOrFail($id);
            return new AuthorResource($author);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Author not found'
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve author',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * List all authors with optional filters.
     */
    public function listAuthors(Request $request)
    {
        $query = Author::with(['books', 'media']);

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('name', 'like', $searchTerm)
                ->orWhere('biography', 'like', $searchTerm);
        }

        // Apply birthdate filter
        if ($request->filled('birthdate')) {
            $query->whereDate('birthdate', $request->birthdate);
        }

        try {
            $authors = $query->get();
            return AuthorResource::collection($authors);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve authors',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a specific author.
     */
    public function delete($id)
    {
        try {
            $author = $this->findAuthorById($id);

            // Clear all associated media collections
            $this->clearAuthorMedia($author);

            // Delete the author
            $author->delete();

            return response()->json(['message' => 'Author deleted successfully'], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('Author not found', Response::HTTP_NOT_FOUND);

        } catch (AuthorizationException $e) {
            return $this->errorResponse('Unauthorized action.', Response::HTTP_FORBIDDEN, $e->getMessage());

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete author', Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    /**
     * Find an author by ID or throw a ModelNotFoundException.
     */
    private function findAuthorById($id)
    {
        return Author::findOrFail($id);
    }

    /**
     * Clear all media collections associated with the author.
     */
    private function clearAuthorMedia(Author $author)
    {
        $mediaCollections = ['profile_image', 'cover_image'];

        foreach ($mediaCollections as $collection) {
            $author->clearMediaCollection($collection);
        }
    }

    /**
     * Generate a standardized error response.
     */
    private function errorResponse($message, $statusCode, $details = null)
    {
        $response = ['error' => $message];

        if ($details) {
            $response['details'] = $details;
        }

        return response()->json($response, $statusCode);
    }
}
