<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PhraseController;
use App\Http\Controllers\Api\V1\EmergencyController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ConversationController;
use App\Http\Controllers\Api\V1\SignLanguageController;
use App\Http\Controllers\Api\V1\AudioController;
use App\Http\Controllers\Api\V1\ImageController;
use App\Http\Controllers\Api\V1\MedicalController;
use App\Http\Controllers\Api\V1\AnalyticsController;
use App\Http\Controllers\Api\V1\AccessibilityController;

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

Route::prefix('v1')->group(function () {
    // Authentication routes (/api/v1/auth/...)
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('logout', 'logout')->middleware('auth:sanctum');
        Route::get('me', 'me')->middleware('auth:sanctum');
        Route::post('reset-password', 'resetPassword');
    });

    // Phrase routes
    Route::controller(PhraseController::class)->group(function () {
        Route::get('phrases', 'index');
        Route::get('phrases/defaults', 'defaults');
        Route::post('phrases', 'store')->middleware('auth:sanctum');
        Route::get('phrases/{phrase}', 'show');
        Route::put('phrases/{phrase}', 'update')->middleware('auth:sanctum');
        Route::delete('phrases/{phrase}', 'destroy')->middleware('auth:sanctum');
    });

    // Emergency routes (protected)
    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(EmergencyController::class)->group(function () {
            Route::get('emergencies', 'index');
            Route::post('emergencies', 'store');
            Route::get('emergencies/active', 'active');
            Route::post('emergencies/trigger', 'trigger');
            Route::get('emergencies/{emergency}', 'show');
            Route::put('emergencies/{emergency}', 'update');
            Route::delete('emergencies/{emergency}', 'destroy');
        });

        // Message routes
        Route::controller(MessageController::class)->group(function () {
            Route::get('messages', 'index');
            Route::post('messages', 'store');
            Route::post('messages/send-phrase', 'sendPhrase');
            Route::get('messages/{message}', 'show');
            Route::put('messages/{message}', 'update');
            Route::delete('messages/{message}', 'destroy');
            Route::get('conversations/{conversation}/messages', 'conversation');
        });

        // User profile routes
        Route::controller(UserController::class)->group(function () {
            Route::get('profile', 'show');
            Route::put('profile', 'update');
            Route::put('profile/accessibility', 'updateAccessibility');
            Route::get('profile/accessibility', 'accessibility');
        });

        Route::prefix('user')->controller(UserController::class)->group(function () {
            Route::get('profile', 'show');
            Route::put('profile', 'update');
        });

        // Conversation routes
        Route::controller(ConversationController::class)->group(function () {
            Route::get('conversations', 'index');
            Route::post('conversations', 'store');
            Route::get('conversations/{conversation}', 'show');
            Route::put('conversations/{conversation}', 'update');
            Route::delete('conversations/{conversation}', 'destroy');
            Route::post('conversations/{conversation}/participants', 'addParticipant');
            Route::delete('conversations/{conversation}/participants/{user}', 'removeParticipant');
            Route::put('conversations/{conversation}/read', 'markAsRead');
        });

        // Sign Language routes
        Route::controller(SignLanguageController::class)->group(function () {
            Route::get('sign-languages/categories', 'categories');
            Route::get('sign-languages/basic', 'basicSigns');
            Route::get('sign-languages/emergency', 'emergencySigns');
            Route::get('sign-languages', 'index');
            Route::post('sign-languages', 'store');
            Route::get('sign-languages/{signLanguage}', 'show');
            Route::put('sign-languages/{signLanguage}', 'update');
            Route::delete('sign-languages/{signLanguage}', 'destroy');
        });

        // Audio routes
        Route::controller(AudioController::class)->group(function () {
            Route::post('audio/speech-to-text', 'speechToText');
            Route::post('audio/text-to-speech', 'textToSpeech');
            Route::get('audio', 'index');
            Route::post('audio', 'store');
            Route::get('audio/{audio}', 'show');
            Route::put('audio/{audio}', 'update');
            Route::delete('audio/{audio}', 'destroy');
        });

        // Image routes
        Route::controller(ImageController::class)->group(function () {
            Route::get('images/profile', 'profile');
            Route::post('images/profile', 'uploadProfile');
            Route::get('images/type/{type}', 'byType');
            Route::get('images', 'index');
            Route::post('images', 'store');
            Route::get('images/{image}', 'show');
            Route::put('images/{image}', 'update');
            Route::delete('images/{image}', 'destroy');
        });

        // Medical routes
        Route::controller(MedicalController::class)->group(function () {
            Route::get('medical-records', 'index');
            Route::post('medical-records', 'store');
            Route::get('medical-records/{medicalRecord}', 'show');
            Route::put('medical-records/{medicalRecord}', 'update');
            Route::delete('medical-records/{medicalRecord}', 'destroy');
            Route::post('medical-records/{medicalRecord}/medications', 'addMedication');
            Route::get('medical-records/{medicalRecord}/medications', 'medications');
            Route::post('medical-records/{medicalRecord}/appointments', 'addAppointment');
            Route::get('medical-records/{medicalRecord}/appointments', 'appointments');
        });

        // Analytics routes
        Route::controller(AnalyticsController::class)->group(function () {
            Route::get('analytics', 'index');
            Route::get('analytics/messages', 'messages');
            Route::get('analytics/emergencies', 'emergencies');
            Route::get('analytics/accessibility', 'accessibility');
            Route::post('analytics/generate-report', 'generateReport');
        });

        // Accessibility routes
        Route::controller(AccessibilityController::class)->group(function () {
            Route::get('accessibility/settings', 'settings');
            Route::put('accessibility/settings', 'updateSettings');
            Route::get('accessibility/recommendations', 'recommendations');
            Route::post('accessibility/test', 'test');
            Route::get('accessibility', 'index');
            Route::post('accessibility', 'store');
            Route::get('accessibility/{accessibilityLog}', 'show');
            Route::put('accessibility/{accessibilityLog}', 'update');
            Route::delete('accessibility/{accessibilityLog}', 'destroy');
        });
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
