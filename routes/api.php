<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\TutCategoryController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\NewsLetterController;

Route::post('register', [UsersController::class, 'registerUser']);
Route::post('login', [UsersController::class, 'loginUser']);
Route::post('forgot-password', [UsersController::class, 'forgotPassword']);
Route::post('reset-password', [UsersController::class, 'resetPassword']);

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::post('news-letter', [NewsLetterController::class, 'subscribe']);
Route::delete('news-letter/{news_letter}', [NewsLetterController::class, 'unsubscribe']);

Route::middleware(['auth:sanctum','active'])->group(function () {
    Route::get('users/{user}', [UsersController::class, 'getUser']);
    Route::put('users/update/password', [UsersController::class, 'updatePassword']);

    Route::get('tutorial/category', [TutCategoryController::class, 'getAllTutCategories']);

    Route::get('tutorials', [TutorialController::class, 'getAllTutorials']);
    Route::get('/tutorials/{slug}/{type}', [TutorialController::class, 'getATutorial']);
}); 

Route::middleware(['auth:sanctum','admin','active'])->group(function(){
    Route::get('users', [UsersController::class, 'getUsers']);
    Route::put('users/{user}', [UsersController::class, 'updateUser']);
    Route::put('users/block/unblock/{user}', [UsersController::class, 'blockUnblockUser']);
    Route::delete('users/{user}', [UsersController::class, 'deleteUser']);

    Route::post('tutorials', [TutorialController::class, 'postTutorial']);
    Route::put('tutorials/{tutorial}', [TutorialController::class, 'updateTutorial']);
    Route::delete('tutorials/{tutorial}', [TutorialController::class, 'deleteTutorial']);

    Route::post('tutorial/category', [TutCategoryController::class, 'postTutorial']);
    Route::get('tutorial/category/{tutorial}', [TutCategoryController::class, 'getATutCategory']);
    Route::put('tutorial/category/{tutorial}', [TutCategoryController::class, 'updateTutCategory']);
    Route::delete('tutorial/category/{tutorial}', [TutCategoryController::class, 'deleteTutCategory']);
});