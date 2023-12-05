<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UploadedVideos;

class VideoManagement extends Controller
{
    public function video_list(Request $request)
    {
        $data = UploadedVideos::with('Creator:id,channel_name')
        ->orderBy("id", "desc")
        ->paginate(10);
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Retrive Done'
        ]);
    }
}
