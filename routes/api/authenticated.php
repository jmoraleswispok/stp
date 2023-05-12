<?php

use Illuminate\Support\Facades\Route;

Route::prefix('authenticated')->middleware(['auth:api'])->group(function () {

});
