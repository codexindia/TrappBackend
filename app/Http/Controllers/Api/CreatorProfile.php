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
        $data = Creator::select('first_name','last_name','channel_name','channel_banner','channel_logo')->find($request->cre_id);
       $vList = UploadedVideos::where([
        'creator_id'=>$request->cre_id,
        'privacy' => 'public'
        ])->orderBy('id','desc')->get('id','title','description','thumbnail','live_api_data','type','views');
        $data['is_followd'] = true;
        $data['follow_counts'] = 34;
        $data['videos_counts'] = 244;
        $data['playlist_count'] = 1;
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
