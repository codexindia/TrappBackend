<?php

use App\Models\CoinTransaction;
use App\Models\VideoAnalytics;
use App\Models\User;
use App\Models\Subscriptions;
use Carbon\Carbon;





if (!function_exists('debit_coin')) {
    function credit_coin(int $user_id, $coins, $desc = null, $user_type = 'user')
    {
        $result = new CoinTransaction;
        $result->reference_id = 'TRP' . time();
        $result->user_id = $user_id;
        $result->user_type = $user_type;
        $result->coins = $coins;
        $result->description = $desc;
        $result->transaction_type = "credit";
        if ($result->save()) {
            if (User::find($user_id)->increment('coins', $coins)) {
                return 1;
            }
        } else {
            return 0;
        }
    }
}



if (!function_exists('subscription_apply')) {

    function subscription_apply($user_id, $sub_id = null)
    {
        $check_exists = Subscriptions::where('user_id', $user_id)->latest()->first();
        if ($check_exists != null) {
            $start_time = Carbon::parse($check_exists->expired_at);
            $end_time = Carbon::parse($check_exists->expired_at)->addMonth();
            $result = Subscriptions::create(
                [
                    'user_id' => $user_id, 'start_at' => $start_time,
                    'expired_at' => $end_time, 'status' => 'active',
                    'subscription_id' => $sub_id
                ]
            );
        } else {
            $start_time = Carbon::now();
            $end_time = Carbon::now()->addMonth();
            $result = Subscriptions::create(
                ['user_id' => $user_id, 'start_at' => $start_time, 'expired_at' => $end_time, 'status' => 'active', 'subscription_id' => $sub_id]
            );
        }

        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
if (!function_exists('remove_subscription')) {

    function remove_subscription($sub_id = null)
    {
        $result = Subscriptions::where([
            'subscription_id' => $sub_id,
        ])->update([
            'status' => 'expired',
            'expired_at' => Carbon::now()
        ]);
        if ($result)
            return 1;
    }
}


if (!function_exists('follow')) {

    function follow($creator_id, $user_id)
    {
        if (VideoAnalytics::where([
            'creator_id' => $creator_id,
            'user_id' => $user_id,
            'action' => 'follow'
        ])->exists()) {
            return unfollow($creator_id, $user_id);
        } else {
            VideoAnalytics::create([
                'creator_id' => $creator_id,
                'user_id' => $user_id,
                'action' => 'follow'
            ]);
            return 1;
        }
    }
}
if (!function_exists('unfollow')) {

    function unfollow($creator_id, $user_id)
    {
        if (!VideoAnalytics::where([
            'creator_id' => $creator_id,
            'user_id' => $user_id,
            'action' => 'follow'
        ])->exists()) {
            return "Error Not Followed";
        } else {
            VideoAnalytics::where([
                'creator_id' => $creator_id,
                'user_id' => $user_id,
                'action' => 'follow',
            ])->delete();
            return 2;
        }
    }
}
if (!function_exists('follow_count')) {

    function follow_count($creator_id)
    {

        return VideoAnalytics::where([
            'creator_id' => $creator_id,
            'action' => 'follow'
        ])->count();
    }
}
if (!function_exists('like')) {

    function like($creator_id, $user_id, $video_id)
    {
        $query = VideoAnalytics::where([
            'creator_id' => $creator_id,
            'user_id' => $user_id,
            'action' => 'like',
        ])->whereJsonContains('attribute', ['video_id' => $video_id]);
        if (!$query->exists()) {
            $query = VideoAnalytics::where([
                'creator_id' => $creator_id,
                'user_id' => $user_id,
                'action' => 'dislike',
            ])->whereJsonContains('attribute', ['video_id' => $video_id]);
            $query->delete();

            VideoAnalytics::create([
                'creator_id' => $creator_id,
                'user_id' => $user_id,
                'action' => 'like',
                'attribute' => json_encode(['video_id' => $video_id])
            ]);
            return 1;
        } {
            $query->delete();
            return 2;
        }
    }
}
if (!function_exists('dislike')) {

    function dislike($creator_id, $user_id, $video_id)
    {
        $query = VideoAnalytics::where([
            'creator_id' => $creator_id,
            'user_id' => $user_id,
            'action' => 'dislike',
        ])->whereJsonContains('attribute', ['video_id' => $video_id]);
        if (!$query->exists()) {

            $query = VideoAnalytics::where([
                'creator_id' => $creator_id,
                'user_id' => $user_id,
                'action' => 'like',
            ])->whereJsonContains('attribute', ['video_id' => $video_id]);
            $query->delete();
            VideoAnalytics::create([
                'creator_id' => $creator_id,
                'user_id' => $user_id,
                'action' => 'dislike',
                'attribute' => json_encode(['video_id' => $video_id])
            ]);
            return 1;
        } {
            $query->delete();
            return 2;
        }
    }
}
