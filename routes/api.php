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

Route::prefix('stp')->group(function () {
    Route::post('test', STPTestController::class);
    Route::post('check-account-balance', [STPTestController::class, 'checkAccountBalance']);
});
