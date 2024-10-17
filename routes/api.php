<?php

use App\Http\Controllers\API\AuthorController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

    // Use apiResource for Category
    Route::apiResource('categories', CategoryController::class);

    // Use apiResource for CategoryGroup
    Route::apiResource('category-groups', CategoryController::class);

    // Add any additional routes here
    Route::get('category-groups', [CategoryController::class, 'categoryGroups']);

    // Add apiResource for author
    Route::apiResource('authors', AuthorController::class);

});

require __DIR__.'/site.php';
require __DIR__.'/auth.php';
