<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;
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

Route::prefix('/auth')
    ->name('auth.')
    ->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');

        Route::post('/forgot-password', [PasswordController::class, 'forgotPassword'])->name('forgot_password');
        Route::post('/reset-password', [PasswordController::class, 'resetPassword'])->name('reset_password');

        Route::middleware('auth')->group(function () {
            Route::put('/change-password', [PasswordController::class, 'changePassword'])->name('change_password');
            
            Route::get('/me', [ProfileController::class, 'show'])->name('profile.show');

            Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::post('/profile/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.upload_photo');
            Route::delete('/profile/delete-photo', [ProfileController::class, 'deletePhoto'])->name('profile.delete_photo');
        });
    });

Route::apiResource('addresses', AddressController::class)->middleware('auth');