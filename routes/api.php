<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\TutCategoryController;
use App\Http\Controllers\TutorialController;

Route::post('register', [UsersController::class, 'registerUser']);
Route::post('login', [UsersController::class, 'loginUser']);
Route::post('forgot-password', [UsersController::class, 'forgotPassword']);
Route::post('reset-password', [UsersController::class, 'resetPassword']);
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::middleware(['auth:sanctum','active'])->group(function () {
    Route::get('users/{user}', [UsersController::class, 'getUser']);
    Route::put('users/update/password', [UsersController::class, 'updatePassword']);
    Route::get('tutorial/category', [TutCategoryController::class, 'getAllTutCategories']);
    Route::get('tutorial', [TutorialController::class, 'getAllTutorials']);
}); 

Route::middleware(['auth:sanctum','admin','active'])->group(function(){
    Route::get('users', [UsersController::class, 'getUsers']);
    Route::put('users/{user}', [UsersController::class, 'updateUser']);
    Route::put('users/block/unblock/{user}', [UsersController::class, 'blockUnblockUser']);
    Route::delete('users/{user}', [UsersController::class, 'deleteUser']);

    Route::post('tutorial', [TutorialController::class, 'postTutorial']);
    Route::get('tutorial/{tutorial}', [TutorialController::class, 'getATutorial']);
    Route::put('tutorial/{tutorial}', [TutorialController::class, 'updateTutorial']);
    Route::delete('tutorial/{tutorial}', [TutorialController::class, 'deleteTutorial']);

    Route::post('tutorial/category', [TutCategoryController::class, 'postTutorial']);
    Route::get('tutorial/category/{tutorial}', [TutCategoryController::class, 'getATutCategory']);
    Route::put('tutorial/category/{tutorial}', [TutCategoryController::class, 'updateTutCategory']);
    Route::delete('tutorial/category/{tutorial}', [TutCategoryController::class, 'deleteTutCategory']);
});