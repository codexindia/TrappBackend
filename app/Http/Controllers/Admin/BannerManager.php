<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerManager extends Controller
{

    public function get_banner_list(Request $request)
    {
        $banner = Banner::orderBy("id", "desc")->get();
        return response()->json([
            'status' => true,
            'data' => $banner,
            'message' => 'Banner Get SuccessFully',
        ]);
    }
    public function create_banner(Request $request)
    {
        $request->validate([
            'banner_image' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048|required',
        ]);
        if ($request->hasFile('banner_image')) {
            $image_path = Storage::put('public/banners', $request->file('banner_image'));
        }
        $banner = Banner::create([
            'img_src' => $image_path,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Banner Created SuccessFully',
        ]);
    }
    public function delete_banner(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:banners,id',
        ]);
        Banner::find($request->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Banner Deleted SuccessFully',
        ]);
    }
}
