<?php
// app/Http/Controllers/API/UserController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array', // Array of permission IDs
            'permissions.*' => 'exists:permissions,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        if ($request->has('permissions')) {
            $user->permissions()->attach($request->permissions);
        }

        return response()->json($user->load(['role', 'permissions']), 201);
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
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:6',
            'role_id' => 'sometimes|required|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('role_id')) {
            $user->role_id = $request->role_id;
        }
        $user->save();

        if ($request->has('permissions')) {
            $user->permissions()->sync($request->permissions);
        }

        return response()->json($user->load(['role', 'permissions']), 200);
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
