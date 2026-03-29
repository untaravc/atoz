<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backoffice\UserController;
use App\Http\Controllers\Backoffice\MenuController;
use App\Http\Controllers\Backoffice\RoleController;
use App\Http\Controllers\Backoffice\MenuRoleController;
use App\Http\Controllers\Backoffice\OfficeController;
use App\Http\Controllers\Backoffice\CategoryController;
use App\Http\Controllers\Backoffice\AuthController;
use App\Http\Controllers\Backoffice\TransactionController;

Route::get('/', function () {
    return response()->json(['status' => true]);
});

Route::post('login', [AuthController::class, 'login']);
Route::get('gen-password', [AuthController::class, 'genPassword']);

Route::middleware('jwt')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('menus', MenuController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('categories', CategoryController::class);

    Route::get('menu-role-permissions', [MenuRoleController::class, 'permissions']);
    Route::patch('menu-role-permissions', [MenuRoleController::class, 'updatePermissions']);
    Route::apiResource('menu-role', MenuRoleController::class);

    Route::apiResource('offices', OfficeController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::get('office-list', [OfficeController::class, 'officeList']);
    Route::get('menu', [MenuController::class, 'menu']);
    Route::get('role-list', [RoleController::class, 'roleList']);
    Route::get('category-list', [CategoryController::class, 'categoryList']);
});
