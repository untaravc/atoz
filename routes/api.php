<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backoffice\UserController;
use App\Http\Controllers\Backoffice\MenuController;

Route::get('/', function () {
    return response()->json(['status' => true]);
});

Route::apiResource('users', UserController::class);
Route::apiResource('menus', MenuController::class);
