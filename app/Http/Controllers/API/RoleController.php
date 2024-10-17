<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
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
        $this->authorizeResource(Role::class, 'role');
    }

    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::withCount('permissions')->get(); // Retrieve roles with permissions count

        return RoleResource::collection($roles)->response()->setStatusCode(200);
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'array', // Array of permission IDs
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create($request->only(['name', 'description']));

        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        $role->load('permissions');

        return new RoleResource($role);
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions');

        return new RoleResource($role);
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'sometimes|required|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update($request->only(['name', 'description']));

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        $role->load('permissions');

        return new RoleResource($role);
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json(['message' => 'Role deleted'], 200);
    }

    /**
     * Assign permissions to the role.
     */
    public function assignPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->permissions);

        $role->load('permissions');

        return new RoleResource($role);
    }
}
