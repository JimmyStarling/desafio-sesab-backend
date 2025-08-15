<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AddressController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('/users/bulk', [UserController::class, 'storeBulk'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'userProfile']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('profiles', ProfileController::class);
    Route::apiResource('addresses', AddressController::class);
    Route::post('logout', [AuthController::class, 'logout']);
});