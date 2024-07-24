<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;

Route::group(['middleware' => ['errorHandler']], function () {
    Route::post('register', [UsersController::class, 'registerUser']);
});