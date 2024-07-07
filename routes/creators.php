<?php
use Illuminate\Support\Facades\Route;
Route::get('test', function() {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', 'https://trapp-creator-panel.vercel.app')
        ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization');
})->middleware('cors');
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
        Route::post('upload', 'upload')->middleware('cors');
        Route::options('upload', function() {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', 'https://trapp-creator-panel.vercel.app')
                ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, X-Auth-Token, Origin, Authorization');
        })->middleware('cors');
        Route::post('get_cat_list', 'get_cat_list');
        Route::post('video_list', 'video_list');
        Route::post('delete', 'delete');
        Route::post('edit', 'edit');
        Route::prefix('live')->group(function(){
            Route::post('create', 'create_live');
            Route::post('fetch', 'fetch_live');
        });
    });
    Route::controller('ChatsController')->prefix('livechat')->group(function () {
        Route::post('/messages','sendMessage');
        Route::post('/fetch','fetch');
      });
      Route::controller('Playlist')->prefix('playlist')->group(function () {
        Route::post('create', 'store');
        Route::post('delete', 'destroy');
        Route::post('fetch', 'index');
    });
});