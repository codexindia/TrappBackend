<?php

namespace App\Http\Controllers\Api;

use ApiVideo\Client\Client;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Http\Discovery\Psr18Client;
use Illuminate\Http\Request;
use App\Models\UploadedVideos;
use App\Models\VideoAnalytics;
use Illuminate\Support\Facades\Log;

class VideosManager extends Controller
{
    public function get_cat_list()
    {
        $data = Category::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'cat retreive'
        ]);
    }
    public function get_vid_by_cat(Request $request)
    {
        $request->validate([
            'cat_id' => 'required',
        ]);
        $data = UploadedVideos::where('cat_id', $request->cat_id)->orderBy('id', 'desc')->paginate(10);
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Video fetched By Cat'
        ]);
    }

    public function get_v_details(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:uploaded_videos,id'
        ]);
        $video_id = $request->video_id;
        $data = UploadedVideos::with('Creator:id,channel_name,channel_logo,first_name,last_name')->where('id', $video_id)->first();
        UploadedVideos::find($request->video_id)->increment('views', 1);
        $data->creator->makeHidden(['email', 'created_at', 'updated_at', 'contact_address', 'first_name', 'last_name', 'phone_number']);

        $data['like'] = 0;
        $data['dislike'] = 0;
        $data['followed'] = 0;
        $data['like_count'] = 0;
        $data['creator']['follow_count'] = follow_count($data->creator->id);
        $query = VideoAnalytics::where([
            'user_id' => $request->user()->id,
            'action' => 'like',
        ])->whereJsonContains('attribute', ['video_id' => $video_id]);
        if ($query->exists())
            $data['like'] = 1;
        $query = VideoAnalytics::where([
            'user_id' => $request->user()->id,
            'action' => 'dislike',
        ])->whereJsonContains('attribute', ['video_id' => $video_id]);
        if ($query->exists())
            $data['dislike'] = 1;
        $query = VideoAnalytics::where([
            'user_id' => $request->user()->id,
            'action' => 'follow',
            'creator_id' => $data->creator->id,
        ]);
        if ($query->exists())
            $data['followed'] = 1;


        $query = VideoAnalytics::where([
            'action' => 'like',
        ])->whereJsonContains('attribute', ['video_id' => $video_id])->count();
        $data['like_count'] = $query;


        if ($data->video_type == "live") {

            $live_api = json_decode($data->live_api_data);
            $data['hls_link'] = $live_api->assets->hls;
        }
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
    public function webhook(Request $data)
    {
        
        return Log::info($data->ip());



        
        switch ($data['type']) {
            case 'live-stream.broadcast.started':
                $update = UploadedVideos::where('video_type','live')->whereJsonContains('live_api_data', ['liveStreamId' => $data['liveStreamId']])->first();
                if ($update != null) {
                    $update->update([
                        'privacy' => 'public',
                    ]);
                }
               
                break;
            case 'live-stream.broadcast.ended':
                $update = UploadedVideos::where('video_type','live')->whereJsonContains('live_api_data', ['liveStreamId' => $data['liveStreamId']])->first();
                if ($update != null) {
                    $update->update([
                        'privacy' => 'private',
                    ]);
                }
               
                break;
            default:
                return 'Received unknown event type ' . $data['type'];
               
        }
    }
}
