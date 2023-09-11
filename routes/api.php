<?php

use App\Http\Controllers\STPTestController;
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

require (__DIR__ . '/api/guest.php');
require (__DIR__ . '/api/authenticated.php');

Route::post('test', STPTestController::class);


Route::post('conciliation', \App\Http\Controllers\ConciliationController::class);
Route::post('check-account-balance', \App\Http\Controllers\CheckAccountBalanceController::class);

Route::post('order', \App\Http\Controllers\OrderController::class);
Route::prefix('order')->group(function () {
    Route::patch('status-changes', \App\Http\Controllers\Order\ChangeStatusController::class);
    Route::post('received', \App\Http\Controllers\Order\ReceiveController::class);
});
