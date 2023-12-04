<?php
use Illuminate\Support\Facades\Route;
Route::controller('AuthManager')->middleware('throttle:api')->prefix('auth')->group(function () {
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
    Route::controller('VideoManagement')->prefix('video')->group(function () {
        Route::post('upload', 'upload');
        Route::post('video_list', 'video_list');
    });
});