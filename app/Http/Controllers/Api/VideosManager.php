<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UploadedVideos;

class VideosManager extends Controller
{
    public function get_v_details(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:uploaded_videos,id'
        ]);
        $video_id = $request->video_id;
        $data = UploadedVideos::with('Creator:id,channel_name')->where('id', $video_id)->first();
      return response()->json([
        'status' => true,
        'data' => $data
      ]);
    }
    public function like(Request $request)
    {
        $request->validate([
            'creator_id' => 'required|exists:creators,id',
            'video_id' => 'required|exists:uploaded_videos,id'
        ]);
        $result = like($request->creator_id, $request->user()->id, $request->video_id);
        if ($result == 1) {
            return response()->json([
                'status' => true,
                'message' => 'Video Liked SuccessFully',
            ]);
        } else if ($result == 2) {
            return response()->json([
                'status' => true,
                'message' => 'Video Liked Removed',
            ]);
        }
    }

    public function dislike(Request $request)
    {
        $request->validate([
            'creator_id' => 'required|exists:creators,id',
            'video_id' => 'required|exists:uploaded_videos,id'
        ]);
        $result = dislike($request->creator_id, $request->user()->id, $request->video_id);
        if ($result == 1) {
            return response()->json([
                'status' => true,
                'message' => 'Video dislike SuccessFully',
            ]);
        } else if ($result == 2) {
            return response()->json([
                'status' => true,
                'message' => 'Video dislike Removed',
            ]);
        }
    }
}
