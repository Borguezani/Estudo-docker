<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\MovieListController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Authentication routes
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
});

// Movies routes (public)
Route::prefix('movies')->group(function () {
    Route::get('/popular', [MovieController::class, 'popular']);
    Route::get('/trending', [MovieController::class, 'trending']);
    Route::get('/top-rated', [MovieController::class, 'topRated']);
    Route::get('/search', [MovieController::class, 'search']);
    Route::get('/discover', [MovieController::class, 'discover']);
    Route::get('/genres', [MovieController::class, 'genres']);
    Route::get('/{id}', [MovieController::class, 'show']);
});

// Movie Lists routes (authenticated)
Route::middleware('auth:api')->prefix('movie-lists')->group(function () {
    Route::get('/', [MovieListController::class, 'index']);
    Route::post('/', [MovieListController::class, 'store']);
    Route::get('/{id}', [MovieListController::class, 'show'])->where('id', '[0-9]+');
    Route::put('/{id}', [MovieListController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/{id}', [MovieListController::class, 'destroy'])->where('id', '[0-9]+');
    
    // Movie items in lists
    Route::post('/{listId}/movies', [MovieListController::class, 'addMovie'])->where('listId', '[0-9]+');
    Route::delete('/{listId}/movies/{itemId}', [MovieListController::class, 'removeMovie'])
        ->where(['listId' => '[0-9]+', 'itemId' => '[0-9]+']);
    Route::put('/{listId}/movies/{itemId}/notes', [MovieListController::class, 'updateMovieNotes'])
        ->where(['listId' => '[0-9]+', 'itemId' => '[0-9]+']);
});

// Public movie lists
Route::get('/public-lists', [MovieListController::class, 'publicLists']);

// User Profile routes
Route::middleware('auth:api')->prefix('profile')->group(function () {
    Route::get('/', [UserProfileController::class, 'profile']);
    Route::put('/', [UserProfileController::class, 'updateProfile']);
    Route::put('/password', [UserProfileController::class, 'changePassword']);
    Route::delete('/avatar', [UserProfileController::class, 'deleteAvatar']);
});

// Public user profiles
Route::prefix('users')->group(function () {
    Route::get('/{userId}', [UserProfileController::class, 'publicProfile'])->where('userId', '[0-9]+');
    Route::get('/{userId}/lists', [UserProfileController::class, 'userPublicLists'])->where('userId', '[0-9]+');
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'service' => 'Movie API Backend'
    ]);
});
