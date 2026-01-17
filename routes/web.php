<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backoffice\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'bo'], function () {
    Route::get('/', [DashboardController::class, 'index']);
});
