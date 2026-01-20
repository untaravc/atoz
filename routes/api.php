<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backoffice\UserController;
use App\Http\Controllers\Backoffice\MenuController;
use App\Http\Controllers\Backoffice\RoleController;
use App\Http\Controllers\Backoffice\MenuRoleController;

Route::get('/', function () {
    return response()->json(['status' => true]);
});

Route::apiResource('users', UserController::class);
Route::apiResource('menus', MenuController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('menu-role', MenuRoleController::class);
Route::get('menu', [MenuController::class, 'menu']);
Route::get('role-list', [RoleController::class, 'roleList']);
