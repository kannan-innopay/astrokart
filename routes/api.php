<?php

use App\Http\Controllers\Api\V1\Admin\AstrologerManagementController;
use App\Http\Controllers\Api\V1\AstrologerController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ConsultationController;
use App\Http\Controllers\Api\V1\ExpertiseController;
use App\Http\Controllers\Api\V1\LanguageController;
use App\Http\Controllers\Api\V1\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public auth
    Route::prefix('auth')->group(function () {
        Route::post('otp/request', [AuthController::class, 'requestOtp']);
        Route::post('otp/verify', [AuthController::class, 'verifyOtp']);
        Route::post('login', [AuthController::class, 'loginWithEmail']);
    });

    // Public astrologer listing
    Route::get('astrologers', [AstrologerController::class, 'index']);
    Route::get('astrologers/{astrologer}', [AstrologerController::class, 'show']);

    // Public reference data
    Route::get('expertises', [ExpertiseController::class, 'index']);
    Route::get('languages', [LanguageController::class, 'index']);

    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        // User profile
        Route::get('user/profile', [UserProfileController::class, 'show']);
        Route::put('user/profile', [UserProfileController::class, 'update']);

        // Astrologer application
        Route::post('astrologer/apply', [AstrologerController::class, 'apply']);

        // Consultations
        Route::post('consultations/request', [ConsultationController::class, 'request']);
        Route::post('consultations/{consultation}/accept', [ConsultationController::class, 'accept']);
        Route::post('consultations/{consultation}/reject', [ConsultationController::class, 'reject']);
        Route::post('consultations/{consultation}/end', [ConsultationController::class, 'end']);
        Route::post('consultations/{consultation}/messages', [ConsultationController::class, 'sendMessage']);
        Route::get('consultations/{consultation}/messages', [ConsultationController::class, 'messages']);
        Route::get('consultations/active', [ConsultationController::class, 'active']);
        Route::get('consultations/history', [ConsultationController::class, 'history']);
        Route::post('consultations/{consultation}/rate', [ConsultationController::class, 'rate']);

        // Astrologer self-service (requires astrologer role)
        Route::middleware('role:astrologer')->prefix('astrologer')->group(function () {
            Route::get('profile', [AstrologerController::class, 'profile']);
            Route::put('profile', [AstrologerController::class, 'updateProfile']);
            Route::put('availability', [AstrologerController::class, 'updateAvailability']);
            Route::post('go-online', [AstrologerController::class, 'goOnline']);
            Route::post('go-offline', [AstrologerController::class, 'goOffline']);
        });

        // Admin routes
        Route::middleware('role:admin,super_admin')->prefix('admin')->group(function () {
            Route::get('astrologers', [AstrologerManagementController::class, 'index']);
            Route::get('astrologers/{astrologer}', [AstrologerManagementController::class, 'show']);
            Route::patch('astrologers/{astrologer}/status', [AstrologerManagementController::class, 'updateStatus']);
        });
    });
});
