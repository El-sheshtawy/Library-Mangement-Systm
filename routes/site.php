<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Books\BookController;
use App\Http\Controllers\Books\CategoryController;
use App\Http\Controllers\Books\CommentController;
use App\Http\Controllers\Books\DownloadController;

/*
|------------------------------------------------------------------
|  -  Books Routes
|------------------------------------------------------------------
*/
// Route::prefix('books')->group(function () {
//     Route::get('/', [BookController::class, 'index'])->name('books.index');          
//     Route::get('/{book}', [BookController::class, 'show'])->name('books.show');       
//     Route::post('/', [BookController::class, 'store'])->name('books.store');          
//     Route::put('/{book}', [BookController::class, 'update'])->name('books.update');   
//     Route::delete('/{book}', [BookController::class, 'destroy'])->name('books.destroy'); 
// });

