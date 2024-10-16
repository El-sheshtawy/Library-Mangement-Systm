<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Books\BookSeriesController;
use App\Http\Controllers\Author\AuthorController;

    /*
    |------------------------------------------------------------------
    | -  Category Routes
    |------------------------------------------------------------------
     */
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->middleware('permission:view-categories');
        Route::get('/{category}', [CategoryController::class, 'show'])->middleware('permission:view-categories');
        Route::post('/', [CategoryController::class, 'store'])->middleware('permission:create-category');
        Route::post('/{category}', [CategoryController::class, 'update'])->middleware('permission:edit-category');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('permission:delete-category');
    });

    /*
    |------------------------------------------------------------------
    | -  Category Groups Routes
    |------------------------------------------------------------------
    */
    Route::prefix('category-groups')->group(function () {
        Route::get('/', [CategoryController::class, 'categoryGroups'])->middleware('permission:view-category');
        Route::post('/', [CategoryController::class, 'storeCategoryGroup'])->middleware('permission:create-category');
        Route::get('/{categoryGroup}', [CategoryController::class, 'showCategoryGroup'])->middleware('permission:view-category');
        Route::post('/{categoryGroup}', [CategoryController::class, 'updateCategoryGroup'])->middleware('permission:edit-category');
        Route::delete('/{categoryGroup}', [CategoryController::class, 'destroyCategoryGroup'])->middleware('permission:delete-category');
    });


  /*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  */

  Route::prefix('book-series')->group(function () {
      Route::get('/', [BookSeriesController::class, 'index']);
      Route::post('/', [BookSeriesController::class, 'store'])->middleware('permission:create-book');
      Route::get('/{bookSeries}', [BookSeriesController::class, 'show']);
      Route::put('/{bookSeries}', [BookSeriesController::class, 'update'])->middleware('permission:edit-book');
      Route::delete('/{bookSeries}', [BookSeriesController::class, 'destroy']);
  });

  /*
  |--------------------------------------------------------------------------
  | Author Routes
  |--------------------------------------------------------------------------
  */
  Route::prefix('author-requests')->group(function () {
      Route::get('/', [AuthorController::class, 'listRequests']);
      Route::post('/create', [AuthorController::class, 'requestAuthor'])->middleware('permission:create-book'); 
      Route::post('/{id}/handle', [AuthorController::class, 'handleRequest']);
      Route::post('/{id}/update', [AuthorController::class, 'updateAuthorRequest'])->middleware('permission:edit-book'); 
      Route::post('/{id}/handle-update', [AuthorController::class, 'handleUpdateRequest']);
  });

  Route::prefix('authors')->group(function () {
      Route::get('/', [AuthorController::class, 'index']); 
      Route::get('/{author}', [AuthorController::class, 'show']); 
      Route::delete('/{id}', [AuthorController::class, 'delete']);
  });
