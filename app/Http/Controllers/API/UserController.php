<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Apply policy-based authorization.
     */
    public function __construct()
    {
        // Simulate user login for testing purposes
        // Remove or modify this in production
        if (app()->environment('local')) {
            Auth::loginUsingId(1);
        }
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the users with search and filter capabilities.
     */
    public function index(Request $request)
    {
        // Initialize the query with eager loading of 'role' and 'permissions'
        $query = User::with('role');

        // Handle search by name using scope
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->searchByName($searchTerm);
        }

        // Handle filter by role using scope
        if ($request->has('role_id')) {
            $roleId = $request->input('role_id');
            $query->filterByRole($roleId);
        }

        // Handle pagination parameters
        $perPage = $request->input('per_page', 10); // Default to 10 per page
        $users = $query->paginate($perPage);

        // Return paginated user resources
        return UserResource::collection($users)->response()->setStatusCode(200);
    }

    /**
     * Store a newly created user, including image upload.
     */
    public function store(StoreUserRequest $request)
    {
        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        // Attach permissions if provided
        if ($request->has('permissions')) {
            $user->permissions()->attach($request->permissions);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $user->addMediaFromRequest('image') // 'image' is the input name
            ->usingName('Profile Image')    // Optional: Assign a name
            ->toMediaCollection('image');    // 'image' is the media collection
        }

        // Load relationships and media
        $user->load(['role', 'permissions', 'media']);

        // Return the created user as a resource
        return (new UserResource($user))->response()->setStatusCode(201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['role', 'permissions', 'media']);
        $user->image_url = $user->getImageUrl();
        return new UserResource($user);
    }


    /**
     * Update the specified user, including image upload.
     */
    public function update(Request $request, User $user)
    {
        // Authorize the update action
        $this->authorize('update', $user);

        // Validate the request
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:6',
            'role_id' => 'sometimes|required|exists:roles,id', // Changed from 'role_name' to 'role_id' for consistency
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Update user attributes
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('role_id')) {
            $user->role_id = $request->role_id;
        }
        $user->save();

        // Assign permissions if provided
        if ($request->has('permissions')) {
            $user->permissions()->sync($request->permissions);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $user->addMediaFromRequest('image')
                ->usingName('Profile Image')
                ->toMediaCollection('image');
        }

        // Load relationships and media
        $user->load(['role', 'permissions', 'media']);

        // Return the updated user as a resource
        return new UserResource($user);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent users from deleting themselves
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'You cannot delete yourself.'], 403);
        }

        // Detach permissions and delete user
        $user->permissions()->detach();
        $user->delete();

        return response()->json(['message' => 'User deleted'], 200);
    }

    /**
     * Assign role to the user.
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->role_id = $request->role_id;
        $user->save();

        // Optionally, sync permissions based on the new role
        // $user->permissions()->sync($user->role->permissions->pluck('id')->toArray());

        return response()->json($user->load('role'), 200);
    }

    /**
     * Assign permissions to the user.
     */
    public function assignPermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $user->permissions()->sync($request->permissions);

        return response()->json($user->load('permissions'), 200);
    }
}
