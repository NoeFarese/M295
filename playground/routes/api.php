<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ClownController;
use App\Http\Controllers\LoginController;
use App\Models\Bike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TopicController;
use App\Models\book;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('responder')->group(function () {
    Route::get('/hi', function () {
        return 'hello world';
    });

    Route::get('/number', function () {
        return random_int(1, 10);
    });

    Route::get('/www', function () {
        return redirect('https://ict-bz.ch/', 302);
    });

    Route::get('/favi', function () {
        return response()->download(public_path('favicon.ico'));
    });

    Route::get('/hi/{name}', function (string $name) {
        return "Hallo $name";
    });

    Route::get('/weather', function () {
        return [
            'city' => 'Luzern',
            'temperature' => 20,
            'wind' => 10,
            'rain' => 0,
        ];
    });

    Route::get('/error', function () {
        return response()->json(['error' => 'Nicht authorisiert!'], 401);
    });

    Route::get('/multiply/{number1}/{number2}', function (int $number1, int $number2) {
        return $number1 * $number2;
    })->whereNumber(['number1', 'number2']);
});

Route::prefix('hallo-velo')->group(function () {
    Route::get('/bikes', function () {
        /*
        $pdo = new PDO('mysql:host=localhost:8889;dbname=playground', 'root', 'root');
        $statement = $pdo->prepare('SELECT * FROM bikes');
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
        */

        return Bike::get();
    });

    Route::get('/bikes/{id}', function (int $id) {
        /*
        $pdo = new PDO('mysql:host=localhost:8889;dbname=playground', 'root', 'root');
        $statement = $pdo->prepare('SELECT * FROM bikes WHERE id = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
        */

        return Bike::find($id);
    })->whereNumber('id');
});

Route::prefix('bookler')->group(function () {
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{id}', [BookController::class, 'show']);

    Route::prefix('/book-finder')->group(function () {
        Route::get('/slug/{slug}', [BookController::class, 'bySlug']);
        Route::get('/year/{year}', [BookController::class, 'byYear']);
        Route::get('/max-pages/{pages}', [BookController::class, 'pages']);
    });

    Route::prefix('/meta')->group(function () {
        Route::get('/count', [BookController::class, 'count']);
        Route::get('/avg-pages', [BookController::class, 'avg']);
    });

    Route::get('/search/{search}', [BookController::class, 'search']);

    Route::get('/dashboard', function () {
        return response()->json([
            'books' => book::count(),
            'pages' => book::sum('pages'),
            'oldest' => book::min('year'),
            'newest' => book::max('year')
        ]);
    });
});

Route::prefix('relationsheep')->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('topics/{slug}/posts', [TopicController::class, 'posts']);
});

Route::prefix('ackerer')->group(function () {
    Route::get('/plant', function () {});
    Route::get('/plant/{slug}', function (string $slug){});
    Route::get('/farms', function (){});
});

Route::prefix('r-rest-y')->group(function () { // Insomnium oder Postman verwenden --> für post & put inhalt als json mitgeben
    Route::get('/clowns', [ClownController::class, 'index']);
    Route::post('/clowns', [ClownController::class, 'store']);
    Route::put('/clowns/{id}', [ClownController::class, 'update']);
    Route::delete('/clowns/{id}', [ClownController::class, 'destroy']);
});

Route::prefix('guardener')->group(function () {
    Route::post('/login', [LoginController::class, 'authenticate']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/secret', [LoginController::class, 'secret']);
    });
});

