<?php

use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UserController;


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

    /*
    |------------------------------------------------------------------
    | -  User Routes
    |------------------------------------------------------------------
     */
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:view-users');
        Route::get('/{user}', [UserController::class, 'show'])->middleware('permission:view-users');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:create-user');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:edit-user');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:delete-user');
    });

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    // Permissions Routes
    Route::apiResource('permissions', PermissionController::class);

    // Roles Routes
    Route::apiResource('roles', RoleController::class);
    Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])
        ->name('roles.assignPermissions');

    // Users Routes
    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/role', [UserController::class, 'assignRole'])
        ->name('users.assignRole');
    Route::post('users/{user}/permissions', [UserController::class, 'assignPermissions'])
        ->name('users.assignPermissions');
});




require __DIR__.'/site.php';
require __DIR__.'/auth.php';
