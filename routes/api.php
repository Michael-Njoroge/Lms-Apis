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
use App\Http\Controllers\DocumentCategoryController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\VideoCategoryController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;

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

// Blog Routes
Route::get('blogs', [BlogController::class, 'getAllBlogs']);
Route::get('blogs/{blog}', [BlogController::class, 'getABlog']);

// Course Category Routes
Route::get('courses-category', [CourseCategoryController::class, 'getAllCourseCategories']);
Route::get('courses-category/{coursecategory}', [CourseCategoryController::class, 'getACourseCategory']);

// Course Routes
Route::get('courses', [CourseController::class, 'getAllCourses']);
Route::get('courses/{course}', [CourseController::class, 'getACourse']);
Route::get('/courses/{type}', [CourseController::class, 'getAllCoursesByCategory']);

//////////////////////////////////////Private User Routes///////////////////////////////////////
Route::middleware(['auth:sanctum','active'])->group(function () {
    // User Routes
    Route::get('users/{user}', [UsersController::class, 'getUser']);
    Route::put('users/update/password', [UsersController::class, 'updatePassword']);

    // Tutorial Category Routes
    Route::get('tutorial/category', [TutCategoryController::class, 'getAllTutCategories']);
    Route::get('tutorial/category/{tutorial}', [TutCategoryController::class, 'getATutCategory']);

    // Document Category Routes
    Route::get('documents/category', [DocumentCategoryController::class, 'getAllDocumentCategories']);
    Route::get('documents/category/{document}', [DocumentCategoryController::class, 'getADocumentCategory']);

    // Blog Category Routes
    Route::get('blogs-category', [BlogCategoryController::class, 'getAllBlogCategories']);
    Route::get('blogs-category/{blogcategory}', [BlogCategoryController::class, 'getABlogCategory']);

    // Video Category Routes
    Route::get('videos-category', [VideoCategoryController::class, 'getAllVideoCategories']);
    Route::get('videos-category/{videocategory}', [VideoCategoryController::class, 'getAVideoCategory']);

    // Tutorial Routes
    Route::get('tutorials', [TutorialController::class, 'getAllTutorials']);
    Route::get('/tutorials/{slug}/{type}', [TutorialController::class, 'getATutorial']);

    // Reviews Routes
    Route::post('reviews', [ReviewsController::class, 'createReview']);
    Route::get('reviews/{review}', [ReviewsController::class, 'getAReview']);

    // Contacts Routes
    Route::post('contacts', [ContactsController::class, 'createContact']);
    Route::get('contacts/{contact}', [ContactsController::class, 'getAContact']);

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
    Route::put('tutorial/category/{tutorial}', [TutCategoryController::class, 'updateTutCategory']);
    Route::delete('tutorial/category/{tutorial}', [TutCategoryController::class, 'deleteTutCategory']);

    // Document Category Routes
    Route::post('documents/category', [DocumentCategoryController::class, 'postDocumentCategory']);
    Route::put('documents/category/{document}', [DocumentCategoryController::class, 'updateDocumentCategory']);
    Route::delete('documents/category/{document}', [DocumentCategoryController::class, 'deleteDocumentCategory']);

    // Blog Category Routes
    Route::post('blogs-category', [BlogCategoryController::class, 'postBlogCategory']);
    Route::put('blogs-category/{blogcategory}', [BlogCategoryController::class, 'updateBlogCategory']);
    Route::delete('blogs-category/{blogcategory}', [BlogCategoryController::class, 'deleteBlogCategory']);

    // Video Category Routes
    Route::post('videos-category', [VideoCategoryController::class, 'postVideoCategory']);
    Route::put('videos-category/{videocategory}', [VideoCategoryController::class, 'updateVideoCategory']);
    Route::delete('videos-category/{videocategory}', [VideoCategoryController::class, 'deleteVideoCategory']);

    // Reviews Routes
    Route::put('reviews/{review}', [ReviewsController::class, 'updateReview']);
    Route::delete('reviews/{review}', [ReviewsController::class, 'deleteReview']);

    // Contact Routes
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

    // Blog Routes
    Route::post('blogs', [BlogController::class, 'postBlog']);
    Route::put('blogs/{blog}', [BlogController::class, 'updateBlog']);
    Route::delete('blogs/{blog}', [BlogController::class, 'deleteBlog']);
});

///////////////////////////////////Private Instructor Routes//////////////////////////////////////////
Route::middleware(['auth:sanctum','active','instructor'])->group(function(){
     // Course Routes
    Route::get('instructor-courses', [CourseController::class, 'getAllCoursesByInstructor']);
});

///////////////////////////////////Private Routes For Both Admin & Instructor//////////////////////////////////////////
Route::middleware(['auth:sanctum','active','both'])->group(function(){
    // Course Category Routes
    Route::post('courses-category', [CourseCategoryController::class, 'postCourseCategory']);
    Route::put('courses-category/{coursecategory}', [CourseCategoryController::class, 'updateCourseCategory']);
    Route::delete('courses-category/{coursecategory}', [CourseCategoryController::class, 'deleteCourseCategory']);

    // Course Routes
    Route::post('courses', [CourseController::class, 'postCourse']);
    Route::put('courses/{course}', [CourseController::class, 'updateCourse']);
    Route::delete('courses/{course}', [CourseController::class, 'deleteCourse']);

    // Lesson Routes
    Route::post('lessons/{course}', [LessonController::class, 'createLesson']);
    // Route::put('courses/{course}', [CourseController::class, 'updateCourse']);
    // Route::delete('courses/{course}', [CourseController::class, 'deleteCourse']);
});