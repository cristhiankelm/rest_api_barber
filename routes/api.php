<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;

use App\Http\Controllers\Api\BarberController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register'])->name('user.register');



// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {
//    Route::get('/random', [BarberController::class, 'createRandom']);

    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/auth/refresh', [AuthController::class, 'refresh'])->name('refresh');

    Route::get('/user', [UserController::class, 'read'])->name('user.read');
    Route::put('/user', [UserController::class, 'update'])->name('user.update');
    Route::get('/user/favorites', [UserController::class, 'getFavorites'])->name('user.getFavorites');
    Route::post('/user/favorite', [UserController::class, 'toggleFavorite'])->name('user.toggleFavorite');
    Route::get('/user/appointments', [UserController::class, 'getAppointments'])->name('user.GetAppointments');

    Route::get('/barbers', [BarberController::class, 'list'])->name('barber.list');
    Route::get('/barber/{id}', [BarberController::class, 'one'])->name('barber.one');
    Route::post('/barber/{id}/appointment', [BarberController::class, 'setAppointment'])->name('barber.setAppointment');

    Route::get('/search', [BarberController::class, 'search'])->name('search');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
