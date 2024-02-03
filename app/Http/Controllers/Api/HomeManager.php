<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UploadedVideos;
class HomeManager extends Controller
{
    public function get_layout(Request $request)
    {
        $normal_video = UploadedVideos::where([
            'video_type' => 'normal',
            'privacy' => 'public'
            ])->inRandomOrder()->limit(10)->get();
        $live_video = UploadedVideos::where([
            'video_type' => 'live',
            'privacy' => 'public'
            ])->with('Creator:id,channel_name,channel_logo')->inRandomOrder()->limit(10)->get(['id','creator_id']);
        $data = array(
            'normal_video' => $normal_video,
            'live_video' => $live_video
        );
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'retrieve done'
        ]);
    }
}
