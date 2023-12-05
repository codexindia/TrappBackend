<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UploadedVideos;
use App\Models\Creator;
class DashboardManager extends Controller
{
    public function get_counts(Request $request)
    {
        $counts = array();
        $counts["users"] = User::count();
        $counts["creators"] = Creator::count();
        $counts["videos"] = UploadedVideos::where('video_type','normal')->count();
        $counts["live"] = UploadedVideos::where('video_type','live')->count();
        //$counts = json_encode($counts);
        return response()->json([
            'status' => true,
            'dash_counts' => $counts,
            'message' => 'Dashboard Retirved',
        ]);
    }
}
