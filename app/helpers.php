<?php

use App\Models\VideoAnalytics;

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
