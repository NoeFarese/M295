<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::fallback( function () {
    if (str_starts_with(request()->path(), 'api')) {
        return response()->json(['error' => 'Not Found'], 404);
    }

    return file_get_contents(public_path('index.html'));
});
