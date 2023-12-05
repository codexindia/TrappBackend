<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\UploadedVideos;
use Illuminate\Http\Request;

class DashboardManager extends Controller
{
    public function get_counts(Request $request)
    {
        $videos = UploadedVideos::where([
            'creator_id'=> $request->user()->id,
            'video_type'=>'normal'
        ])->count();
        $lives =UploadedVideos::where([
            'creator_id'=> $request->user()->id,
            'video_type'=>'live'
        ])->count();
        $counts = array();
        $counts["revenue"] = 0;
        $counts["followers"] = 50;
        $counts["videos"] = $videos;
        $counts["live"] = $lives;
        //$counts = json_encode($counts);
        return response()->json([
            'status' => true,
            'dash_counts' => $counts,
            'message' => 'Dashboard Retirved',
        ]);
    }
}
