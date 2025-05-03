<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\SystemSetting\SystemSettingController;
use App\Http\Controllers\API\Package\PackageController;
use App\Http\Controllers\API\Service\ServiceController;
use App\Http\Controllers\API\Credit\CreditController;
use App\Http\Controllers\API\Project\ProjectController;
use App\Http\Controllers\API\Subscription\SubscriptionController;
use App\Http\Controllers\API\ProjectPin\ProjectPinController;
use App\Http\Controllers\API\About\AboutController;
use App\Http\Controllers\API\Contact\ContactController;
use Illuminate\Support\Facades\Route;



// Common routes
Route::get('/system-setting', [SystemSettingController::class, 'systemSetting']);

// Packages routes
Route::get('/packages',[PackageController::class,'index']);
Route::get('/package/{id}',[PackageController::class,'show']);

// Services routes
Route::get('/service',[ServiceController::class,'index']);
Route::get('/service/{id}',[ServiceController::class,'show']);

// Credits routes
Route::get('/credit',[CreditController::class,'index']);
Route::get('/credit/{id}',[CreditController::class,'show']);

// Projects routes
Route::get('/project',[ProjectController::class,'index']);
Route::get('/project/{id}',[ProjectController::class,'show']);

// About us routes
Route::get('/about-us',[AboutController::class,'index']);
Route::get('/about-us/{id}',[AboutController::class,'show']);

// Contact Mail routes
Route::post('/contact/send', [ContactController::class, 'send']);

// Payment Success routes
Route::get('/success-subscription', [SubscriptionController::class, 'successSubscription'])
    ->name('subscription.success');

// Guest Routes
Route::group(['middleware' => 'guest:sanctum'], function ($router) {
    Route::post('login',[LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('resend_otp', [RegisterController::class, 'resend_otp']);
    Route::post('verify_otp', [RegisterController::class, 'verify_otp']);
    Route::post('forgot-password', [RegisterController::class, 'forgot_password']);
    Route::post('forgot-verify-otp', [RegisterController::class, 'forgot_verify_otp']);
    Route::post('reset-password', [RegisterController::class, 'reset_password']);
});

Route::group(['middleware' => 'auth:sanctum'], function ($router) {
    // common routes
    Route::get('/user-detail', [LoginController::class, 'userDetails']);
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::post('/payment-intent', [SubscriptionController::class, 'createPaymentIntent'])->name('payment.intent');
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::post('/cancel-subscription', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::get('/subscription-status', [SubscriptionController::class, 'getSubscriptionStatus'])->name('subscription.status');
    Route::get('/user-subscription-check', [SubscriptionController::class, 'userSubscriptionCheck'])->name('user.subscription.check');

    // Projects routes
    Route::get('/project-pin',[ProjectPinController::class,'index']);
    Route::get('/project-pin/{id}',[ProjectPinController::class,'show']);
    Route::post('/project-pin/store',[ProjectPinController::class,'store']);
    Route::post('/project-pin/destroy/{id}',[ProjectPinController::class,'destroy']);

});


