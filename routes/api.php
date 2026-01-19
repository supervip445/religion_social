<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\DhammaController;
use App\Http\Controllers\Admin\DonationController;
use App\Http\Controllers\Admin\BiographyController;
use App\Http\Controllers\Admin\MonasteryController;
use App\Http\Controllers\Admin\MonasteryBuildingDonationController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Public\PublicPostController;
use App\Http\Controllers\Public\PublicDhammaController;
use App\Http\Controllers\Public\PublicBiographyController;
use App\Http\Controllers\Public\PublicDonationController;
use App\Http\Controllers\Public\PublicMonasteryController;
use App\Http\Controllers\Public\PublicMonasteryBuildingDonationController;
use App\Http\Controllers\Public\PublicLessonController;
use App\Http\Controllers\Public\LikeController;
use App\Http\Controllers\Public\CommentController;
use App\Http\Controllers\Public\PublicBannerController;
use App\Http\Controllers\Public\PublicAuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BannerTextController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ViewController;
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Api\V1\Chat\AdminToUserChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public API routes (no authentication required)
Route::prefix('public')->group(function () {
    // Posts
    Route::get('/posts', [PublicPostController::class, 'index']);
    Route::get('/posts/{id}', [PublicPostController::class, 'show']);
    
    // Dhamma Talks
    Route::get('/dhammas', [PublicDhammaController::class, 'index']);
    Route::get('/dhammas/{id}', [PublicDhammaController::class, 'show']);
    
    // Biographies
    Route::get('/biographies', [PublicBiographyController::class, 'index']);
    Route::get('/biographies/{id}', [PublicBiographyController::class, 'show']);
    
    // Donations (approved only)
    Route::get('/donations', [PublicDonationController::class, 'index']);
    Route::get('/donations/{id}', [PublicDonationController::class, 'show']);
    
    // Monasteries
    Route::get('/monasteries', [PublicMonasteryController::class, 'index']);
    
    // Monastery Building Donations
    Route::get('/monastery-building-donations', [PublicMonasteryBuildingDonationController::class, 'index']);
    
    // Likes/Dislikes
    Route::post('/likes/toggle', [LikeController::class, 'toggle']);
    Route::get('/likes/counts', [LikeController::class, 'counts']);
    
    // Comments
    Route::get('/comments', [CommentController::class, 'index']);
    Route::post('/comments', [CommentController::class, 'store']);
    
    // Banners
    Route::get('/banners', [PublicBannerController::class, 'getBanners']);
    Route::get('/banner-texts', [PublicBannerController::class, 'getBannerTexts']);
    
    // Lessons
    Route::get('/lessons', [PublicLessonController::class, 'index']);
    Route::get('/lessons/{id}', [PublicLessonController::class, 'show']);
    
    // Public User Authentication
    Route::post('/register', [PublicAuthController::class, 'register']);
    Route::post('/login', [PublicAuthController::class, 'login']);
    
    // Protected public user routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [PublicAuthController::class, 'profile']);
        Route::post('/logout', [PublicAuthController::class, 'logout']);
        Route::prefix('chat')->group(function () {
        Route::get('messages', [AdminToUserChatController::class, 'index']);
        Route::post('messages', [AdminToUserChatController::class, 'store']);
    });
    });
});

// Admin authentication routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    
    // Protected admin routes
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('/check', [AdminAuthController::class, 'check']);
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/profile', [AdminAuthController::class, 'profile']);
        Route::put('/profile', [AdminAuthController::class, 'updateProfile']);
        Route::post('/change-password', [AdminAuthController::class, 'changePassword']);
        
        // Categories
        Route::apiResource('categories', CategoryController::class);
        
        // Posts
        Route::apiResource('posts', PostController::class);
        
        // Dhamma Talks
        Route::apiResource('dhammas', DhammaController::class);
        
        // Donations
        Route::apiResource('donations', DonationController::class);
        Route::post('/donations/{id}/approve', [DonationController::class, 'approve']);
        Route::post('/donations/{id}/reject', [DonationController::class, 'reject']);
        
        // Biographies
        Route::apiResource('biographies', BiographyController::class);
        
        // Monasteries
        Route::apiResource('monasteries', MonasteryController::class);
        
        // Monastery Building Donations
        Route::apiResource('monastery-building-donations', MonasteryBuildingDonationController::class);
        
        // Contacts
        Route::apiResource('contacts', ContactController::class)->except(['store']);
        Route::post('/contacts/{id}/read', [ContactController::class, 'markAsRead']);
        
        // Banners
        Route::apiResource('banners', BannerController::class);
        
        // Banner Texts
        Route::apiResource('banner-texts', BannerTextController::class);
        
        // Academic Years
        Route::apiResource('academic-years', AcademicYearController::class);
        
        // Subjects
        Route::apiResource('subjects', SubjectController::class);
        
        // Classes
        Route::apiResource('classes', ClassController::class);
        
        // Lessons
        Route::apiResource('lessons', LessonController::class);
        
        // Users (for teachers dropdown)
        Route::get('/users', [UserController::class, 'index']);
        
        // Views
        Route::get('/views', [ViewController::class, 'getViews']);
        Route::get('/views/stats', [ViewController::class, 'getStats']);
        
        // Admin Chat
        Route::prefix('chat')->group(function () {
            Route::get('/users', [AdminChatController::class, 'getUsers']);
            Route::get('/users/{userId}/messages', [AdminChatController::class, 'getMessages']);
            Route::post('/users/{userId}/messages', [AdminChatController::class, 'sendMessage']);
        });
    });
});
