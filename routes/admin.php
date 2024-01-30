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
        Route::post('unban_user', 'unban_user');
    });
    Route::controller('CreatorManagement')->prefix('creator')->group(function () {
        Route::post('get_creator/{query_data}', 'get_creator');
        Route::post('add_creator', 'add_creator');
        Route::post('delete_creator','delete_creator');
        Route::post('ban_creator','ban_creator');
    });
    Route::controller('BannerManager')->prefix('banner')->group(function () {
        Route::post('get_banner', 'get_banner_list');
        Route::post('create_banner', 'create_banner');
        Route::post('delete', 'delete_banner');
    });
    Route::controller('VideoManagement')->prefix('video')->group(function () {
        Route::post('video_list', 'video_list');
        Route::post('delete', 'delete');
        Route::post('create_cat', 'create_cat');
        Route::post('cat_list', 'cat_list');
        Route::post('cat_edit', 'cat_edit');
    });
    Route::controller('StickersManagement')->prefix('sticker')->group(function () {
        Route::post('create', 'create_new');
        Route::post('list', 'listSticker');
        Route::post('delete', 'deleteSticker');
    });
    Route::controller('WalletManage')->prefix('wallet')->group(function () {
        Route::post('create', 'create_new');
        Route::post('delete', 'deleteCoinBundle');
        Route::post('fetch', 'listCoinBundle');
    });
});
