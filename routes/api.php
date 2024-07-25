<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::group(['middleware' => ['errorHandler']], function () {
    Route::post('register', [UsersController::class, 'registerUser']);
    Route::post('login', [UsersController::class, 'loginUser']);
    Route::get('users/{user}', [UsersController::class, 'getUser']);
}); 

Route::middleware(['auth:sanctum','admin'])->group(function(){
    Route::put('users/{user}', [UsersController::class, 'updateUser']);
    Route::delete('users/{user}', [UsersController::class, 'deleteUser']);
    Route::get('users', [UsersController::class, 'getUsers']);
});