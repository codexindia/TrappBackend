<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller('AuthManager')->prefix('auth')->group(function () {
    Route::post('login', 'login_submit');
    Route::post('forget_pass', 'forget_pass');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::controller('ProfileManager')->prefix('profile')->group(function () {
        Route::post('get_profile', 'get_profile');
        Route::post('edit_profile', 'edit_profile');
    });
    Route::controller('DashboardManager')->prefix('dashboard')->group(function () {
        Route::post('get_counts', 'get_counts');
    });
});
