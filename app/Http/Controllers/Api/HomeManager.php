<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UploadedVideos;
class HomeManager extends Controller
{
    public function get_layout(Request $request)
    {
        $normal_video = UploadedVideos::where("video_type", 'normal')->inRandomOrder()->limit(10)->get();
        $live_video = UploadedVideos::where("video_type", 'live')->inRandomOrder()->limit(10)->get();
        $data = array(
            'normal_video' => $normal_video,
            '$live_video' => $live_video
        );
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'retrieve done'
        ]);
    }
}
