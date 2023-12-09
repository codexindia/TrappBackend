<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
class BannerGet extends Controller
{
    public function get_all(Request $request){
        $banner = Banner::orderBy("id","desc")->get();
        return response()->json([
            'status' => true,
            'data' => $banner,
            'message' => 'retrieve done'
        ]);
    }
}
