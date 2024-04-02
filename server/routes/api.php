<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersFileController;
use App\Http\Middleware\CheckFileExistsMiddleware;
use App\Http\Middleware\CheckFileForUserMiddleware;
use App\Http\Middleware\CheckFilesExistsMiddleware;
use App\Http\Middleware\CheckRequestFileExtensionMiddleware;
use App\Http\Middleware\CheckUserCanWriteMiddleware;
use App\Http\Middleware\CheckUserFileRelationExistsMiddleware;
use App\Http\Middleware\CheckUserIsOwnerOfFileForMoveMiddleware;
use App\Http\Middleware\CheckUserIsOwnerOfFileForWrite;
use App\Http\Middleware\CheckUserIsOwnerOfFileForWriteMiddleware;
use App\Http\Middleware\CheckUserIsOwnerOfFileMiddleware;
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

            // Only for user who can write this file
            Route::middleware([CheckUserCanWriteMiddleware::class, CheckUserIsOwnerOfFileForMoveMiddleware::class])->group(function () {
                // Update file
                Route::put('/{id}', [FileController::class, 'update']);

                // Update file's content in storage
                Route::post('/{id}/content', [FileController::class, 'updateContent'])->middleware(CheckRequestFileExtensionMiddleware::class);;

                // Delete file
                Route::delete('/{id}', [FileController::class, 'destroy']);
            });
        });
    });
});



// ********************************
// *** Что нужно сделать? ***
// * Сделано/не сделано: ✅ ❌
// TODO - Реализовать работу с папками
//  TODO - Реализовать функционал создания папки (по указанному маршруту или без) 
//  TODO - Реализовать функционал просмотра файлов по конкретной папке
//  TODO - Реализовать удаление папки

// ! На работу с папками будут выделяться отдельные роуты
// !    Создание папки:
// !        - Создант папку по указанному (или нет) маршруту в хранилище
// !        - В БД записывается как файл с типом 'dir' и пустым полем 'size'
// !            * Дальшнеее поле 'size' высчитывается в виде суммы всех файлов/папок в этой папке
// !    Редактирование папки:
// !        - Как и у файлов, отредактировать можно только поля 'original_name' и 'uri'
// !            * Т.е. можно только переименовывать и перемещать папку
// !    Удаление папки:
// !        - Точно так же, как и с обычным файлом
// !        - Удаляются все дочерние папки и файлы
// !
// !    Права доступа:
// !        - Не распространяется на файлы в папке
// !            * Добавление/редактирование/удаление прав доступа на все файлы в папке должно быть предусмотрено на клиенте

// ? Подумать нат тем, что делать при автоматическом создании папки, если такая не найдена,
// ?    Как сохранять все автоматически созданные папки? 
// ********************************



// UsersFiles
Route::prefix('users-files')->group(function () {
    Route::middleware('jwt.auth')->group(function () {
        // Get all user-file relations
        Route::get('/', [UsersFileController::class, 'index']);

        // Only if user is owner of file
        Route::middleware(CheckUserIsOwnerOfFileMiddleware::class)->group(function () {
            // Store user-file relation
            Route::post('/', [UsersFileController::class, 'store']);

            // Only if file exists
            Route::middleware(CheckUserFileRelationExistsMiddleware::class)->group(function () {
                // Show single user-file relation
                Route::get('/{id}', [UsersFileController::class, 'show']);

                // Update user-file relation
                Route::put('/{id}', [UsersFileController::class, 'update']);

                // Delete user-file relation
                Route::delete('/{id}', [UsersFileController::class, 'destroy']);
            });
        });
    });
});