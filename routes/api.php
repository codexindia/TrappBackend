<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::controller('AuthManager')->prefix('auth')->group(function () {
    Route::post('/login', 'login');
    Route::post('login/send_otp', 'SendOTP');

    Route::post('resend_otp', 'SendOTP');

    Route::post('signup', 'signup');
    Route::post('signup/send_otp', 'signup_otp');
    Route::post('logout', 'logout')->middleware('auth:sanctum');
});
Route::middleware(['auth:sanctum','user.check'])->group(function () {
    Route::controller('UserManager')->prefix('user')->group(function () {
        Route::post('/get_current_user', 'get_current_user');
        Route::post('/update_user', 'update_user');
    });
    Route::controller('BannerGet')->prefix('banner')->group(function () {
        Route::post('/get_all', 'get_all');
       
    });
});
