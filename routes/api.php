<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\TutCategoryController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\VideosController;
use App\Http\Controllers\DocumentationController;

//////////////////////////////////////Open Routes////////////////////////////////
// Auth Routes
Route::post('register', [UsersController::class, 'registerUser']);
Route::post('login', [UsersController::class, 'loginUser']);
Route::post('forgot-password', [UsersController::class, 'forgotPassword']);
Route::post('reset-password', [UsersController::class, 'resetPassword']);

// Google Auth Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// News Letter Routes
Route::post('news-letter', [NewsLetterController::class, 'subscribe']);
Route::delete('news-letter/{news_letter}', [NewsLetterController::class, 'unsubscribe']);

// Reviews Routes
Route::get('reviews', [ReviewsController::class, 'getReviews']);

// Contacts Routes
Route::get('contacts', [ContactsController::class, 'getContacts']);

// Videos Routes
Route::get('videos', [VideosController::class, 'getVideos']);
Route::get('videos/{video}', [VideosController::class, 'getAVideo']);

// Documentation Routes
Route::get('documentations', [DocumentationController::class, 'getDocumentations']);
Route::get('documentations/{documentation}', [DocumentationController::class, 'getADocumentation']);

//////////////////////////////////////Private User Routes///////////////////////////////////////
Route::middleware(['auth:sanctum','active'])->group(function () {
    // User Routes
    Route::get('users/{user}', [UsersController::class, 'getUser']);
    Route::put('users/update/password', [UsersController::class, 'updatePassword']);

    // Tutorial Category Routes
    Route::get('tutorial/category', [TutCategoryController::class, 'getAllTutCategories']);

    // Tutorial Routes
    Route::get('tutorials', [TutorialController::class, 'getAllTutorials']);
    Route::get('/tutorials/{slug}/{type}', [TutorialController::class, 'getATutorial']);

    // Reviews Routes
    Route::post('reviews', [ReviewsController::class, 'createReview']);

    // Contacts Routes
    Route::post('contacts', [ContactsController::class, 'createContact']);
}); 

///////////////////////////////////Private Admin Routes//////////////////////////////////////////
Route::middleware(['auth:sanctum','active','admin'])->group(function(){
    // User Routes
    Route::get('users', [UsersController::class, 'getUsers']);
    Route::put('users/{user}', [UsersController::class, 'updateUser']);
    Route::put('users/block/unblock/{user}', [UsersController::class, 'blockUnblockUser']);
    Route::delete('users/{user}', [UsersController::class, 'deleteUser']);

    // Tutorial Routes
    Route::post('tutorials', [TutorialController::class, 'postTutorial']);
    Route::put('tutorials/{tutorial}', [TutorialController::class, 'updateTutorial']);
    Route::delete('tutorials/{tutorial}', [TutorialController::class, 'deleteTutorial']);

    // Tutorial Category Routes
    Route::post('tutorial/category', [TutCategoryController::class, 'postTutorial']);
    Route::get('tutorial/category/{tutorial}', [TutCategoryController::class, 'getATutCategory']);
    Route::put('tutorial/category/{tutorial}', [TutCategoryController::class, 'updateTutCategory']);
    Route::delete('tutorial/category/{tutorial}', [TutCategoryController::class, 'deleteTutCategory']);

    // Reviews Routes
    Route::get('reviews/{review}', [ReviewsController::class, 'getAReview']);
    Route::put('reviews/{review}', [ReviewsController::class, 'updateReview']);
    Route::delete('reviews/{review}', [ReviewsController::class, 'deleteReview']);

    // Contact Routes
    Route::get('contacts/{contact}', [ContactsController::class, 'getAContact']);
    Route::put('contacts/{contact}', [ContactsController::class, 'updateContact']);
    Route::delete('contacts/{contact}', [ContactsController::class, 'deleteContact']);

    // Videos Routes
    Route::post('videos', [VideosController::class, 'postVideo']);
    Route::put('videos/{video}', [VideosController::class, 'updateVideo']);
    Route::delete('videos/{video}', [VideosController::class, 'deleteVideo']);

    // Documentation Routes
    Route::post('documentations', [DocumentationController::class, 'postDocumentation']);
    Route::put('documentations/{documentation}', [DocumentationController::class, 'updateDocumentation']);
    Route::delete('documentations/{documentation}', [DocumentationController::class, 'deleteDocumentation']);
});