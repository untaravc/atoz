<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backoffice\DashboardController;

Route::get('/', function () {
    return view('pages.home');
});

Route::group(['prefix' => 'bo'], function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/{any}', [DashboardController::class, 'index'])->where('path', '([A-z\d\-\/_.]+)?');
});
