<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['client'])->group(function () {
    require (__DIR__ . '/guest/authenticate.php');
});
