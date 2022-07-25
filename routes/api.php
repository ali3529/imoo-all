<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Properties\PayrexxCallbackController;
use App\Http\Controllers\Properties\PropertiesController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//@formatter:off
Route::prefix('v1/')->group(function () {
    Route::post('email/verify/{user}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend']);
    Route::post('properties/insertion/create', [PropertiesController::class, 'create']);

    Route::get('payrexx/callback/success', [PayrexxCallbackController::class, 'success'])->name('payrexx.success');
    Route::get('payrexx/callback/failed', [PayrexxCallbackController::class, 'failed'])->name('payrexx.failed');
    Route::get('payrexx/callback/cancel', [PayrexxCallbackController::class, 'cancel'])->name('payrexx.cancel');

    Route::prefix('details/')->group(function () {
        Route::post('categories',[ApiController::class, 'categories']);
        Route::post('countries',[ApiController::class, 'countries']);
        Route::post('cities',[ApiController::class, 'cities']);
        Route::post('features',[ApiController::class, 'features']);
        Route::post('facilities',[ApiController::class, 'facilities']);
        Route::post('propertyStatus',[ApiController::class, 'propertyStatus']);
        Route::post('propertyPackages',[ApiController::class, 'propertyPackages']);
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', [LoginController::class, 'logout']);

        Route::get('user', [UserController::class, 'current']);
        Route::post('user/email/verify', [UserController::class, 'sendEmailVerification']);
        Route::post('user/phone/verify/send', [UserController::class, 'sendPhoneVerification']);
        Route::post('user/phone/verify', [VerificationController::class, 'verifyPhone'])->name('verification.verify.phone');

        Route::patch('settings/profile', [ProfileController::class, 'update']);
        Route::post('settings/profile/upload_avatar', [ProfileController::class, 'uploadAvatar']);
        Route::patch('settings/password', [PasswordController::class, 'update']);
        Route::post('properties/insertion/details', [PropertiesController::class, 'details']);
        Route::post('properties/insertion/update', [PropertiesController::class, 'update']);
        Route::post('properties/insertion/updateFiles', [PropertiesController::class, 'updateFiles']);
        Route::post('properties/insertion/package-update', [PropertiesController::class, 'packageUpdate']);
        Route::post('properties/insertion/propertyPackage',[PropertiesController::class, 'propertyPackage']);
        Route::post('properties/insertion/publish', [PropertiesController::class, 'publish']);
        Route::post('properties/my', [PropertiesController::class, 'myProperties'])->name('property.my');
        Route::post('properties/{property}/delete', [PropertiesController::class, 'delete']);
    });

    Route::group(['middleware' => 'guest:api'], function () {
        Route::post('login', [LoginController::class, 'login']);
        Route::post('register', [RegisterController::class, 'register']);

        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
        Route::post('password/reset', [ResetPasswordController::class, 'reset']);

        Route::post('oauth/{driver}', [OAuthController::class, 'redirect']);
        Route::get('oauth/{driver}/callback', [OAuthController::class, 'handleCallback'])->name('oauth.callback');
    });
});
//@formatter:off
