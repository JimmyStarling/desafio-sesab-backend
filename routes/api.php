<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AddressController;

Route::apiResource('users', UserController::class);
Route::apiResource('profiles', ProfileController::class);
Route::apiResource('addresses', AddressController::class);