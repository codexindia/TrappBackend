<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VideoAnalytics;
use App\Models\Creator;
use App\Models\UploadedVideos;

class CreatorProfile extends Controller
{
    public function channelView(Request $request)
    {
        $request->validate([
            'cre_id' => 'required|exists:creators,id'
        ]);
        $data = Creator::select('first_name', 'last_name', 'channel_name', 'channel_banner', 'channel_logo')->find($request->cre_id);
        $vList = UploadedVideos::where([
            'creator_id' => $request->cre_id,
            'privacy' => 'public'
        ])->orderBy('id', 'desc')->get(['id', 'title', 'thumbnail', 'video_loc', 'video_type', 'views']);
       
        $query = VideoAnalytics::where([
            'user_id' => $request->user()->id,
            'action' => 'follow',
            'creator_id' => $request->cre_id
        ]);
        if ($query->exists())
        $data['is_followed'] = true;
        else
        $data['is_followed'] = false;
        
        $data['follow_counts'] = follow_count($request->cre_id);
        $data['videos_counts'] = $vList->count();
        $data['playlist_count'] = 0;
        $data['videosOrLives'] =  $vList;
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Creator Channel Retrieve Success'
        ]);
    }
    public function follow(Request $request)
    {
        $request->validate(['creator_id' => 'required|exists:creators,id']);
        $result = follow($request->creator_id, $request->user()->id);
        if ($result == 1) {
            return response()->json([
                'status' => true,
                'message' => 'follow success'
            ]);
        } elseif ($result == 2) {
            return response()->json([
                'status' => true,
                'message' => 'unfollow success'
            ]);
        } else
            return response()->json([
                'status' => false,
                'message' => $result
            ]);
    }
}
