<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Apply policy-based authorization.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::with(['role', 'permissions'])->get();
        return response()->json($users, 200);
    }

    /**
     * Store a newly created user, including image upload.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array', // Array of permission IDs
            'permissions.*' => 'exists:permissions,id',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // New validation rule
        ]);

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

        // Return the created user with relationships and media
        return response()->json($user, 201);
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return response()->json($user->load(['role', 'permissions']), 200);
    }

    /**
     * Update the specified user.
     */
    // app/Http/Controllers/API/UserController.php

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:6',
            'role_name' => 'sometimes|required|string|exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validation for image
        ]);

        // Update user attributes
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('role_name')) {
            $roleName = $request->role_name;
            $role = Role::where('name', $roleName)->firstOrFail();
            $user->role()->associate($role);
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

        return response()->json($user->load(['role', 'permissions', 'media']), 200);
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
