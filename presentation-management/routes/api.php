<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentApiAuthController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\AvatarController;
use App\Http\Controllers\Api\ClassroomApiController;
use App\Http\Controllers\Api\AssignmentApiController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AiController;
use App\Http\Controllers\Api\PublicController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Student Authentication API
Route::prefix('auth')->group(function () {
    Route::post('/register', [StudentApiAuthController::class, 'register']);
    Route::post('/login', [StudentApiAuthController::class, 'login']);
    Route::post('/logout', [StudentApiAuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Student Profile API (protected - students only)
Route::middleware(['auth:sanctum', 'role:student'])->prefix('student')->group(function () {
    // Profile & Dashboard
    Route::get('/profile', [StudentApiController::class, 'profile']);
    Route::get('/classes', [StudentApiController::class, 'classes']);
    Route::get('/assignments', [StudentApiController::class, 'assignments']);
    Route::get('/grades', [StudentApiController::class, 'grades']);
    Route::get('/progress', [StudentApiController::class, 'progress']);
    Route::get('/courses', [StudentApiController::class, 'courses']);
    
    // Classroom details
    Route::get('/classes/{id}', [ClassroomApiController::class, 'show']);
    
    // Assignment details & submission
    Route::get('/assignments/{id}', [AssignmentApiController::class, 'show']);
    Route::post('/assignments/{id}/submit', [AssignmentApiController::class, 'submit']);
    
    // Avatar management
    Route::post('/avatar/upload', [AvatarController::class, 'upload']);
    Route::delete('/avatar/delete', [AvatarController::class, 'delete']);
});

// Authentication routes with rate limiting
Route::post('/auth/login', [StudentApiAuthController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute to prevent brute force

Route::post('/auth/register', [StudentApiAuthController::class, 'register'])
    ->middleware('throttle:3,60'); // 3 registrations per hour to prevent spam

// AI Analysis routes with rate limiting
Route::post('/ai/analyze', [AiController::class, 'analyze'])
    ->middleware(['auth:sanctum', 'throttle:10,1']); // 10 analyses per minute
Route::post('/ai/analyze-video', [AiController::class, 'analyzeVideo'])
    ->middleware(['auth:sanctum', 'throttle:5,1']); // 5 video analysis per minute (expensive!)
Route::post('/ai/analyze-video/', [AiController::class, 'analyzeVideo'])
    ->middleware(['auth:sanctum', 'throttle:5,1']); // With trailing slash

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/payment/create-link', [PaymentController::class, 'createPaymentLink']);
    Route::post('/payment/check-transaction', [PaymentController::class, 'checkTransaction']);
});

// Webhook không cần auth sanctum, nhưng cần bảo mật IP hoặc Token (để mở public cho SePay call)
Route::post('/payment/webhook', [App\Http\Controllers\Api\PaymentController::class, 'webhook']);

// R2 Test Routes (Development only - remove in production)
Route::get('/r2/test-upload', [App\Http\Controllers\Api\R2TestController::class, 'testUpload']);
Route::get('/r2/list-files', [App\Http\Controllers\Api\R2TestController::class, 'listFiles']);

// Public APIs
Route::get('/public/courses', [App\Http\Controllers\Api\PublicController::class, 'courses']);
Route::get('/public/team', [App\Http\Controllers\Api\PublicController::class, 'teamMembers']);
