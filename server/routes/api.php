<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckFileExistsMiddleware;
use App\Http\Middleware\CheckFileForUserMiddleware;
use App\Http\Middleware\CheckFilesExistsMiddleware;
use App\Http\Middleware\CheckUserMiddleware;
use App\Http\Middleware\DatabaseTransactionMiddleware;
use App\Http\Middleware\ValidateReceivedFilesMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authorization
Route::group([
    'prefix' => 'auth',
    'middleware' => 'api',
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('refresh', [AuthController::class, 'refresh']);
    Route::get('logout', [AuthController::class, 'logout']);
});


// Users
Route::prefix('users')->group(function () {
    // Create new user
    Route::post('/', [UserController::class, 'store']);

    // Only for authorized users
    Route::middleware('jwt.auth')->group(function () {
        // Get all users
        Route::get('/', [UserController::class, 'index']);

        // Get single user
        Route::get('/{id}', [UserController::class, 'show']);

        // Only for this user
        Route::middleware(CheckUserMiddleware::class)->group(function () {
            // Update user
            Route::put('/{id}', [UserController::class, 'update']);

            // Delete user
            Route::delete('/{id}', [UserController::class, 'destroy']);
        });
    });
});


// Files
Route::prefix('files')->group(function () {
    Route::middleware('jwt.auth')->group(function () {
        // Get all files
        Route::get('/', [FileController::class, 'index']);

        // Only with non-repeated files
        Route::middleware(DatabaseTransactionMiddleware::class)->group(function () {
            // Create new file
            Route::post('/', [FileController::class, 'store']);
        });

        // Only if file exists and available for user
        Route::middleware([CheckFileExistsMiddleware::class, CheckFileForUserMiddleware::class])->group(function () {
            // Show single file
            Route::get('/{id}', [FileController::class, 'show']);

            // Update file
            Route::put('/{id}', [FileController::class, 'update']);

            // Delete file
            Route::delete('/{id}', [FileController::class, 'destroy']);
        });
        
    });
});