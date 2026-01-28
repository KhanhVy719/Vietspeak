<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentApiAuthController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\AvatarController;
use App\Http\Controllers\Api\ClassroomApiController;
use App\Http\Controllers\Api\AssignmentApiController;

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

Route::post('/ai/analyze', [App\Http\Controllers\Api\AiController::class, 'analyze'])->middleware('auth:sanctum');
Route::post('/ai/analyze-video', [App\Http\Controllers\Api\AiController::class, 'analyzeVideo'])->middleware('auth:sanctum');
Route::post('/ai/analyze-video/', [App\Http\Controllers\Api\AiController::class, 'analyzeVideo'])->middleware('auth:sanctum'); // With trailing slash

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/payment/create-link', [App\Http\Controllers\Api\PaymentController::class, 'createPaymentLink']);
    Route::post('/payment/check-transaction', [App\Http\Controllers\Api\PaymentController::class, 'checkTransaction']);
});

// Webhook không cần auth sanctum, nhưng cần bảo mật IP hoặc Token (để mở public cho SePay call)
Route::post('/payment/webhook', [App\Http\Controllers\Api\PaymentController::class, 'webhook']);

// Public APIs
Route::get('/public/courses', [App\Http\Controllers\Api\PublicController::class, 'courses']);
