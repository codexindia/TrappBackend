<?php

use Illuminate\Http\Request;
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
    Route::controller('UserManagement')->prefix('user')->group(function () {
        Route::post('get_users/{query_data}', 'get_users');
        Route::post('delete_user', 'delete_user');
        Route::post('ban_user', 'ban_user');
    });
    Route::controller('CreatorManagement')->prefix('creator')->group(function () {
        Route::post('get_creator/{query_data}', 'get_creator');
    });
});
