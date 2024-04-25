<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TransactionController;
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

Route::post('/login', [LoginController::class, 'login']);

Route::prefix('/transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index']);
    Route::put('/{id}/switch-type', [TransactionController::class, 'switchTypeOfTransaction']);
    Route::delete('/{id}', [TransactionController::class, 'destroy']);
    Route::get('/totals', [TransactionController::class, 'getTotals']);
});

Route::prefix('/categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{category}', [CategoryController::class, 'show'])->whereNumber('category');
    Route::put('/{id}', [CategoryController::class, 'editCategory']);
    Route::get('/{category}/transactions', [CategoryController::class, 'getTransactionsByCategory']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth', [LoginController::class, 'checkAuth']);
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::post('/transactions', [TransactionController::class, 'store']);

    Route::prefix('/users')->group(function () {
        Route::get('/my-account', [UserController::class, 'showMyAccount']);
        Route::delete('/my-account', [UserController::class, 'destroyMyAccount']);
    });
});
