<?php

use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\CourseCategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\DocumentCategoryController;
use App\Http\Controllers\EmailVerifyController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\NewsLetterController;
use App\Http\Controllers\ProjectCategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QNAController;
use App\Http\Controllers\ReviewsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\TutCategoryController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VideoCategoryController;
use App\Http\Controllers\VideosController;
use App\Http\Controllers\WorkWithUsController;
use Illuminate\Support\Facades\Route;

//////////////////////////////////////Open Routes////////////////////////////////
// Auth Routes
Route::post('register', [UsersController::class, 'registerUser']);
Route::post('login', [UsersController::class, 'loginUser']);
Route::post('forgot-password', [UsersController::class, 'forgotPassword']);
Route::post('reset-password', [UsersController::class, 'resetPassword']);
Route::post('/email/resend', [EmailVerifyController::class, 'resend'])->name('verification.resend');
Route::get('/email/verify/{id}/{hash}', [EmailVerifyController::class, 'verify'])->name('verification.verify');

// Social Auth Routes
Route::get('redirect/{provider}', [SocialController::class, 'redirectToProvider']);
Route::post('callback/{provider}', [SocialController::class, 'handleProviderCallback']);

Route::middleware(['throttle:global'])->group(function () {
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

	// Project Routes
	Route::get('projects', [ProjectController::class, 'getAllProjects']);
	Route::get('projects/{project}', [ProjectController::class, 'getAProject']);

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

	// Lesson Routes
	Route::get('lessons/{course}', [LessonController::class, 'getLessons']);
	Route::get('lessons/{course}/{lesson}', [LessonController::class, 'getALesson']);

	// Work With Us Routes
	Route::post('works', [WorkWithUsController::class, 'postWorkDetails']);

	// Qna Session Routes
	Route::get('posts', [QNAController::class, 'getAllPosts']);
	Route::get('posts/{post}', [QNAController::class, 'getPost']);

	// Tag Routes
	Route::get('tags', [QNAController::class, 'getAllTags']);
	Route::get('tags/{tag}', [QNAController::class, 'getATag']);

	// Qna Comment Routes
	Route::get('get-comments', [QNAController::class, 'getAllComments']);
	Route::get('get-comments/{comment}', [QNAController::class, 'getAComment']);
});

//////////////////////////////////////Private User Routes///////////////////////////////////////
Route::middleware(['auth:sanctum', 'active'])->group(function () {
	// User Routes
	Route::post('logout', [UsersController::class, 'logOut']);
	Route::get('users/{user}', [UsersController::class, 'getUser']);
	Route::put('users/update/password', [UsersController::class, 'updatePassword']);
	Route::get('login/history', [UsersController::class, 'getLoginHistory']);

	//2fa auth
	Route::post('2fa/prepare', [TwoFactorController::class, 'prepareTwoFactor'])->name('2fa.prepare');
	Route::post('2fa/confirm', [TwoFactorController::class, 'confirmTwoFactor'])->name('2fa.confirm');
	Route::get('2fa/recovery-codes', [TwoFactorController::class, 'showRecoveryCodes'])->name('2fa.recovery.codes');

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

	// Project Category Routes
	Route::get('projects-category', [ProjectCategoryController::class, 'getAllProjectCategories']);
	Route::get('projects-category/{projectcategory}', [ProjectCategoryController::class, 'getAProjectCategory']);

	// Tutorial Routes
	Route::get('tutorials', [TutorialController::class, 'getAllTutorials']);
	Route::get('/tutorials/{slug}/{type}', [TutorialController::class, 'getATutorial']);

	// Reviews Routes
	Route::post('reviews', [ReviewsController::class, 'createReview']);
	Route::get('reviews/{review}', [ReviewsController::class, 'getAReview']);

	// Contacts Routes
	Route::post('contacts', [ContactsController::class, 'createContact']);

	// Sessions Routes
	Route::post('sessions', [SessionController::class, 'bookSession']);

	// Qna Session Routes
	Route::post('post-question', [QNAController::class, 'createPost'])->middleware('limit:20,30');
	Route::post('post-answer/{post}', [QNAController::class, 'createAnswer'])->middleware('limit:10,30');
	Route::put('posts/{post}', [QNAController::class, 'updatePost'])->middleware('limit:10,30');
	Route::delete('posts/{post}', [QNAController::class, 'deletePost'])->middleware('limit:10,30');

	// Qna Comment Routes
	Route::post('post-comments', [QNAController::class, 'postComment'])->middleware('limit:5,30');
	Route::delete('delete-comments', [QNAController::class, 'deleteComment'])->middleware('limit:5,30');

	// Qna Vote Routes
	Route::post('post-votes', [QNAController::class, 'castVote'])->middleware('limit:5,30');

});

///////////////////////////////////Private Admin Routes//////////////////////////////////////////
Route::middleware(['auth:sanctum', 'active', 'admin'])->group(function () {
	// User Routes
	Route::get('users', [UsersController::class, 'getUsers']);
	Route::put('users/{user}', [UsersController::class, 'updateUser']);
	Route::delete('users/{user}', [UsersController::class, 'deleteUser']);
	Route::post('users/status/{user}', [UsersController::class, 'changeUserStatus']);

	// Tutorial Routes
	Route::post('tutorials', [TutorialController::class, 'postTutorial'])->middleware('limit:100,60');
	Route::put('tutorials/{tutorial}', [TutorialController::class, 'updateTutorial'])->middleware('limit:100,60');
	Route::delete('tutorials/{tutorial}', [TutorialController::class, 'deleteTutorial'])->middleware('limit:100,60');

	// Tutorial Category Routes
	Route::post('tutorial/category', [TutCategoryController::class, 'postTutorial'])->middleware('limit:50,30');
	Route::put('tutorial/category/{tutorial}', [TutCategoryController::class, 'updateTutCategory'])->middleware('limit:50,30');
	Route::delete('tutorial/category/{tutorial}', [TutCategoryController::class, 'deleteTutCategory'])->middleware('limit:50,30');

	// Document Category Routes
	Route::post('documents/category', [DocumentCategoryController::class, 'postDocumentCategory'])->middleware('limit:30,15');
	Route::put('documents/category/{document}', [DocumentCategoryController::class, 'updateDocumentCategory'])->middleware('limit:30,15');
	Route::delete('documents/category/{document}', [DocumentCategoryController::class, 'deleteDocumentCategory'])->middleware('limit:30,15');

	// Blog Category Routes
	Route::post('blogs-category', [BlogCategoryController::class, 'postBlogCategory'])->middleware('limit:20,15');
	Route::put('blogs-category/{blogcategory}', [BlogCategoryController::class, 'updateBlogCategory'])->middleware('limit:20,15');
	Route::delete('blogs-category/{blogcategory}', [BlogCategoryController::class, 'deleteBlogCategory'])->middleware('limit:20,15');

	// Video Category Routes
	Route::post('videos-category', [VideoCategoryController::class, 'postVideoCategory'])->middleware('limit:20,15');
	Route::put('videos-category/{videocategory}', [VideoCategoryController::class, 'updateVideoCategory'])->middleware('limit:20,15');
	Route::delete('videos-category/{videocategory}', [VideoCategoryController::class, 'deleteVideoCategory'])->middleware('limit:20,15');

	// Project Category Routes
	Route::post('projects-category', [ProjectCategoryController::class, 'postProjectCategory'])->middleware('limit:20,15');
	Route::put('projects-category/{projectcategory}', [ProjectCategoryController::class, 'updateProjectCategory'])->middleware('limit:20,15');
	Route::delete('projects-category/{projectcategory}', [ProjectCategoryController::class, 'deleteProjectCategory'])->middleware('limit:20,15');

	// Reviews Routes
	Route::put('reviews/{review}', [ReviewsController::class, 'updateReview'])->middleware('limit:20,15');
	Route::delete('reviews/{review}', [ReviewsController::class, 'deleteReview'])->middleware('limit:20,15');

	// Contact Routes
	Route::get('contacts/{contact}', [ContactsController::class, 'getAContact'])->middleware('limit:20,15');
	Route::put('contacts/{contact}', [ContactsController::class, 'updateContact'])->middleware('limit:20,15');
	Route::delete('contacts/{contact}', [ContactsController::class, 'deleteContact'])->middleware('limit:20,15');

	// Session Routes
	Route::get('sessions', [SessionController::class, 'getSessions'])->middleware('limit:20,15');
	Route::get('sessions/{session}', [SessionController::class, 'getASession'])->middleware('limit:20,15');
	Route::put('sessions/{session}', [SessionController::class, 'updateSession'])->middleware('limit:20,15');
	Route::delete('sessions/{session}', [SessionController::class, 'deleteSession'])->middleware('limit:20,15');

	// Videos Routes
	Route::post('videos', [VideosController::class, 'postVideo'])->middleware('limit:20,15');
	Route::put('videos/{video}', [VideosController::class, 'updateVideo'])->middleware('limit:20,15');
	Route::delete('videos/{video}', [VideosController::class, 'deleteVideo'])->middleware('limit:20,15');

	// Project Routes
	Route::post('projects', [ProjectController::class, 'postProject'])->middleware('limit:20,15');
	Route::put('projects/{project}', [ProjectController::class, 'updateProject'])->middleware('limit:20,15');
	Route::delete('projects/{project}', [ProjectController::class, 'deleteProject'])->middleware('limit:20,15');

	// Documentation Routes
	Route::post('documentations', [DocumentationController::class, 'postDocumentation'])->middleware('limit:20,15');
	Route::put('documentations/{documentation}', [DocumentationController::class, 'updateDocumentation'])->middleware('limit:20,15');
	Route::delete('documentations/{documentation}', [DocumentationController::class, 'deleteDocumentation'])->middleware('limit:20,15');

	// Blog Routes
	Route::post('blogs', [BlogController::class, 'postBlog'])->middleware('limit:20,15');
	Route::put('blogs/{blog}', [BlogController::class, 'updateBlog'])->middleware('limit:20,15');
	Route::delete('blogs/{blog}', [BlogController::class, 'deleteBlog'])->middleware('limit:20,15');

	// Work With Us Routes
	Route::get('works', [WorkWithUsController::class, 'getWorkDetails'])->middleware('limit:20,15');
	Route::get('works/{workdetail}', [WorkWithUsController::class, 'getAWorkDetail'])->middleware('limit:20,15');
	Route::put('works/{workdetail}', [WorkWithUsController::class, 'updateWorkDetail'])->middleware('limit:20,15');
	Route::delete('works/{workdetail}', [WorkWithUsController::class, 'deleteWorkDetail'])->middleware('limit:20,15');

	// Tag Routes
	Route::post('tags', [QNAController::class, 'createTag']);
	Route::put('tags/{tag}', [QNAController::class, 'updateTag']);
	Route::delete('tags/{tag}', [QNAController::class, 'deleteTag']);

	// Role Routes
	Route::put('assign-role/{user}', [UsersController::class, 'assignRole']);
	Route::put('remove-role/{user}', [UsersController::class, 'removeRole']);
	Route::post('roles', [RoleController::class, 'createRole'])->middleware('limit:20,15');
	Route::get('roles', [RoleController::class, 'getRoles'])->middleware('limit:20,15');
	Route::get('roles/{role}', [RoleController::class, 'getARole'])->middleware('limit:20,15');
	Route::put('roles/{role}', [RoleController::class, 'updateRole'])->middleware('limit:20,15');
	Route::delete('roles/{role}', [RoleController::class, 'deleteRole'])->middleware('limit:20,15');
});

///////////////////////////////////Private Instructor Routes//////////////////////////////////////////
Route::middleware(['auth:sanctum', 'active', 'instructor'])->group(function () {
	// Course Routes
	Route::get('instructor-courses', [CourseController::class, 'getAllCoursesByInstructor']);
});

///////////////////////////////////Private Routes For Both Admin & Instructor//////////////////////////////////////////
Route::middleware(['auth:sanctum', 'active', 'both'])->group(function () {
	// Course Category Routes
	Route::post('courses-category', [CourseCategoryController::class, 'postCourseCategory'])->middleware('limit:20,15');
	Route::put('courses-category/{coursecategory}', [CourseCategoryController::class, 'updateCourseCategory'])->middleware('limit:20,15');
	Route::delete('courses-category/{coursecategory}', [CourseCategoryController::class, 'deleteCourseCategory'])->middleware('limit:20,15');

	// Course Routes
	Route::post('courses', [CourseController::class, 'postCourse'])->middleware('limit:20,15');
	Route::put('courses/{course}', [CourseController::class, 'updateCourse'])->middleware('limit:20,15');
	Route::delete('courses/{course}', [CourseController::class, 'deleteCourse'])->middleware('limit:20,15');

	// Lesson Routes
	Route::post('lessons/{course}', [LessonController::class, 'createLesson'])->middleware('limit:20,15');
	Route::put('lessons/{course}/{lesson}', [LessonController::class, 'updateLesson'])->middleware('limit:20,15');
	Route::delete('lessons/{course}/{lesson}', [LessonController::class, 'deleteLesson'])->middleware('limit:20,15');
});
