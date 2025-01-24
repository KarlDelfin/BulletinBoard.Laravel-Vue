<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('Post', [PostController::class, 'index']);
Route::get('Post/{id}', [PostController::class, 'show']);
Route::post('Post', [PostController::class, 'store']);
Route::put('Post/{id}', [PostController::class, 'update']);
Route::delete('Post', [PostController::class, 'destroy']);
