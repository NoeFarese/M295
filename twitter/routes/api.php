<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/tweets', [TweetController::class, 'index']);
Route::post('/login', [LoginController::class, 'login']);

Route::prefix('/users')->group(function () {
    Route::get('/{user}', [UserController::class, 'show'])->whereNumber('user');
    Route::get('/{user}/tweets',[UserController::class, 'tweets']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth', [LoginController::class, 'checkAuth']);
    Route::any('/logout', [LoginController::class, 'logout']); //eigentlich ein POST aber Twitter Frontend macht GET
    Route::post('/tweets', [TweetController::class, 'store']);
    Route::get('/me', [UserController::class, 'me']);
    Route::put('/me', [UserController::class, 'updateMe']);
    Route::delete('/me', [UserController::class, 'deleteMe']);
    Route::post('/tweets/{tweet}/like', [TweetController::class, 'like'], []);
    Route::get('/users/top', [UserController::class, 'topUsers']);
    Route::get('/users/new', [UserController::class, 'newUsers']);
});
