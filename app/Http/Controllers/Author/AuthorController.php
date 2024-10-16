<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\AuthorRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\AuthorResource;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AuthorController extends Controller
{

    public function __construct()
    {
        Auth::loginUsingId(1); 

        $this->authorizeResource(AuthorRequest::class, 'authorRequest');
    }

    public function requestAuthor(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:authors',
                'biography' => 'nullable|string',
                'birthdate' => 'nullable|date',
                'image' => 'nullable|image',
                'copyright' => 'nullable', 
            ]);

            $validated['user_id'] = Auth::id();
            $validated['status'] = 'pending';

            $authorRequest = AuthorRequest::create($validated);

            if ($request->hasFile('image')) {
                $authorRequest->addMedia($request->file('image'))->toMediaCollection('author_requests');
            } else {
                $authorRequest->addMedia('http://127.0.0.1:8000/assets/images/static/person.png')->toMediaCollection('author_requests');
            }

            if ($request->hasFile('copyright')) { 
                $authorRequest->addMedia($request->file('copyright'))->toMediaCollection('copyright');
            }

            return response()->json(['message' => 'Author request submitted successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to submit author request'], Response::HTTP_BAD_REQUEST);
        }
    }


    public function listRequests()
    {
        try {
            $requests = AuthorRequest::where('status', 'pending')->get();
            return response()->json($requests);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve requests'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function handleRequest($id, Request $request)
    {
        try {
            $authorRequest = AuthorRequest::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:approved,rejected',
            ]);

            if ($validated['status'] === 'approved') {
                $author = Author::create([
                    'name' => $authorRequest->name,
                    'biography' => $authorRequest->biography,
                    'birthdate' => $authorRequest->birthdate,
                ]);

                if ($authorRequest->hasMedia('author_requests')) {
                    $media = $authorRequest->getFirstMedia('author_requests');
                    $media->copy($author, 'authors');
                }

                if ($authorRequest->hasMedia('copyright')) { 
                    $copyrightMedia = $authorRequest->getFirstMedia('copyright');
                    $copyrightMedia->copy($author, 'author_copyrights');
                }

                $authorRequest->delete();

                return response()->json(['message' => 'Author request approved and author created'], Response::HTTP_OK);
            } else {
                $authorRequest->update(['status' => 'rejected']);
                return response()->json(['message' => 'Author request rejected'], Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to handle request'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateAuthorRequest(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:authors,name,' . $id,
                'biography' => 'nullable|string',
                'birthdate' => 'nullable|date',
                'image' => 'nullable|image',
            ]);

            $validated['user_id'] = Auth::id();
            $validated['status'] = 'pending';
            $validated['author_id'] = $id;

            $authorRequest = AuthorRequest::create($validated);

            if ($request->hasFile('image')) {
                $authorRequest->addMedia($request->file('image'))->toMediaCollection('author_requests');
            }

            return response()->json(['message' => 'Author update request submitted successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to submit author update request'], Response::HTTP_BAD_REQUEST);
        }
    }


    public function handleUpdateRequest($id, Request $request)
    {
        try {
            $authorRequest = AuthorRequest::findOrFail($id);
             $this->authorize('handleRequest', $authorRequest);

            $validated = $request->validate([
                'status' => 'required|in:approved,rejected',
            ]);

            if ($validated['status'] === 'approved') {
                $author = Author::findOrFail($authorRequest->author_id);
                
                $author->update([
                    'name' => $authorRequest->name,
                    'biography' => $authorRequest->biography,
                    'birthdate' => $authorRequest->birthdate,
                ]);

                if ($authorRequest->hasMedia('author_requests')) {
                    $media = $authorRequest->getFirstMedia('author_requests');
                    $author->clearMediaCollection('authors');
                    $media->copy($author, 'authors');
                }

                $authorRequest->delete();

                return response()->json(['message' => 'Author update approved and changes applied'], Response::HTTP_OK);
            } else {
                $authorRequest->update(['status' => 'rejected']);
                return response()->json(['message' => 'Author update request rejected'], Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to handle update request'], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show($id)
    {
        try {
            $author = Author::findOrFail($id);
            return new AuthorResource($author);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Author not found', 'details' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function index(Request $request)
    {
        try {
            $query = Author::query();

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('biography', 'like', "%{$search}%");
                });
            }

            if ($request->has('status')) {
                $status = $request->input('status');
                $query->whereHas('authorRequests', function ($q) use ($status) {
                    $q->where('status', $status);
                });
            }

            if ($request->has('birthdate')) {
                $birthdate = $request->input('birthdate');
                $query->where('birthdate', $birthdate);
            }

            $authors = $query->get();

            return AuthorResource::collection($authors);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve authors', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete($id)
    {
        try {
            $author = Author::findOrFail($id);

            if ($author->hasMedia('authors')) {
                $author->clearMediaCollection('authors');
            }

            $author->delete();

            return response()->json(['message' => 'Author deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete author'], Response::HTTP_BAD_REQUEST);
        }
    }

}
