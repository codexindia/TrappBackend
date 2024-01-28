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
    Route::controller('HomeManager')->prefix('home')->group(function () {
        Route::post('/get_layout', 'get_layout');
    });
    Route::controller('HomeManager')->prefix('home')->group(function () {
        Route::post('/get_layout', 'get_layout');
    });
    Route::controller('CreatorProfile')->prefix('creator')->group(function () {
        Route::post('/follow', 'follow');
        Route::post('/channelView', 'channelView');
        Route::post('/un_follow', 'un_follow');
    });
    Route::controller('VideosManager')->prefix('video')->group(function () {
        Route::post('/get_cat_list', 'get_cat_list');
        Route::post('/get_vid_by_cat', 'get_vid_by_cat');
        
        Route::post('/like', 'like');
        Route::post('/get_v_details', 'get_v_details');
        Route::post('/dislike', 'dislike');
    });
    Route::controller('PaypalSubcription')->prefix('subscriptions')->group(function () {
      Route::post('/test','test');
    });
    Route::controller('StripeController')->prefix('payment')->group(function () {
        Route::post('/CallSubscription','CallSubsCription');
        Route::post('/CheckSubscription','CheckSubscription');
        Route::post('/BuyCoins','BuyCoins');
        Route::any('/webhook','webhook')->withoutMiddleware(['auth:sanctum','user.check']);
      });
    Route::controller('ChatsController')->prefix('livechat')->group(function () {
        Route::post('/messages','sendMessage');
        Route::post('/fetch','fetch');
      });
      Route::controller('StickersManagement')->prefix('stickers')->group(function () {
        Route::post('/fetch','fetch');
      });
});
