<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::post('register', [UsersController::class, 'registerUser']);
Route::post('login', [UsersController::class, 'loginUser']);
Route::post('forgot-password', [UsersController::class, 'forgotPassword']);
Route::post('reset-password', [UsersController::class, 'resetPassword']);

Route::middleware(['auth:sanctum','active'])->group(function () {
    Route::get('users/{user}', [UsersController::class, 'getUser']);
    Route::put('users/update/password', [UsersController::class, 'updatePassword']);
}); 

Route::middleware(['auth:sanctum','admin','active'])->group(function(){
    Route::get('users', [UsersController::class, 'getUsers']);
    Route::put('users/{user}', [UsersController::class, 'updateUser']);
    Route::put('users/block/unblock/{user}', [UsersController::class, 'blockUnblockUser']);
    Route::delete('users/{user}', [UsersController::class, 'deleteUser']);
});