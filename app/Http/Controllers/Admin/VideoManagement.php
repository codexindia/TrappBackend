<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoAnalytics;
use Illuminate\Http\Request;
use App\Models\UploadedVideos;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class VideoManagement extends Controller
{
    public function video_list(Request $request)
    {
        $data = UploadedVideos::with('Creator:id,channel_name')
            ->orderBy("id", "desc")
            ->paginate(10);
        // $data['dislike_count'] = 0;
        // $data['like_count'] = 0;
        // $query = VideoAnalytics::where([
        //     'action' => 'like',
        // ])->count();
        // $data['like_count'] = $query;
        // $query = VideoAnalytics::where([
        //     'action' => 'dislike',
        // ])->count();
        // $data['dislike_count'] = $query;
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Retrive Done'
        ]);
    }
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:uploaded_videos,id'
        ]);
        UploadedVideos::where([
            'id' => $request->id,
        ])->delete();
        return response()->json([
            'status' => true,
            'message' => 'video deleted successfully'
        ]);
    }
    public function create_cat(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'image|required',
        ]);
        if ($request->hasFile('image')) {
            $image_path = Storage::put('public/videos/category', $request->file('image'));
            // $update_values['profile_pic'] = $image_path;
        }
        Category::create([
            'title' => $request->title,
            'image' => $image_path,
        ]);
        return response([
            'status' => true,
            'message' => 'Category Added SuccessFully',
        ]);
    }
    public function cat_list(Request $request)
    {
        $data = Category::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'cat retreive'
        ]);
    }
    public function cat_edit(Request $request)
    {
        $request->validate([
            'cat_id' => 'required',
            'title' => 'required'
        ]);
        $data = Category::find($request->cat_id);

        $update_value = array(
            'title' => $request->title,
        );
        if ($request->hasFile('image')) {
            $update_value['image'] = Storage::put('public/videos/category', $request->file('image'));
            // $update_values['profile_pic'] = $image_path;
        }
        $data->update($update_value);
        return response()->json([
            'status' => true,

            'message' => 'cat updated'
        ]);
    }
    public function cat_delete(Request $request)
    {
        // $cat = Category::find($request->cat_id)->delete();
       return Storage::delete('public/videos/category/Sag695wD6o6QQ6BZoTYEXnAEllkIOWATM4OSPXps.png');
        
        return response()->json([
            'status' => true,
            'message' => 'cat Deleted'
        ]);
    }
}
