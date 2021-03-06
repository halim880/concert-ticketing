<?php

use Illuminate\Support\Facades\Route;
use App\http\Controllers\ConsertsController;
use App\http\Controllers\OrdersController;
use App\http\Controllers\ConsertsOrderController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia\Inertia::render('Dashboard');
})->name('dashboard');

Route::resource('consert', ConsertsController::class);

Route::post('/consert/{id}/orders', [ConsertsOrderController::class, 'store']);

Route::get('/orders/{confirmationNumber}', [OrdersController::class, 'show']);