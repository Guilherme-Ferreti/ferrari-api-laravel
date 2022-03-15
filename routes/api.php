<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SearchCepController;
use App\Http\Controllers\TimeOptionController;
use App\Http\Controllers\PaymentSituationController;

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

Route::prefix('/contacts')
    ->name('contacts.')
    ->controller(ContactController::class)
    ->group(function () {
        Route::post('/', 'store')->name('store');

        Route::middleware('auth')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/my-contacts', 'myContacts')->name('my_contacts');
                Route::get('/{contact}', 'show')->name('show');
                Route::delete('/{contact}', 'destroy')->name('destroy');
            });
    });

Route::middleware('auth')
    ->group(function () {
        Route::apiResource('addresses', AddressController::class);
        Route::get('/addresses/search-cep/{cep}', SearchCepController::class)->name('addresses.search_cep');
    });

Route::prefix('/time-options')
    ->name('time_options.')
    ->controller(TimeOptionController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        
        Route::middleware('auth')
            ->group(function () {
                Route::post('/', 'store')->name('store');
                Route::delete('/{timeOption}', 'destroy')->name('destroy');
                Route::post('/{timeOption}', 'restore')->withTrashed()->name('restore');
            });
    });

Route::prefix('/payment-situations')
    ->name('payment_situations.')
    ->controller(PaymentSituationController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{paymentSituation}', 'show')->name('show');

        Route::middleware('auth')
            ->group(function () {
                Route::post('/', 'store')->name('store');
                Route::put('/{paymentSituation}', 'update')->name('update');
                Route::delete('/{paymentSituation}', 'destroy')->name('destroy');
                Route::post('/{paymentSituation}', 'restore')->withTrashed()->name('restore');
            });
    });

Route::prefix('/services')
    ->name('services.')
    ->controller(ServiceController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{service}', 'show')->name('show');

        Route::middleware('auth')
            ->group(function () {
                Route::post('/', 'store')->name('store');
                Route::put('/{service}', 'update')->name('update');
                Route::delete('/{service}', 'destroy')->name('destroy');
                Route::post('/{service}', 'restore')->withTrashed()->name('restore');
            });
    });

Route::prefix('/schedules')
    ->name('schedules.')
    ->controller(ScheduleController::class)
    ->middleware('auth')
    ->group(function () {
        Route::post('/', 'store');
    });