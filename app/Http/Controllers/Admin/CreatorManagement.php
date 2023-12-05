<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\UploadedVideos;
use Illuminate\Http\Request;

class CreatorManagement extends Controller
{
    public function get_creator(Request $request)
    {

        if ($request->query_data == "all") {
            $creator = Creator::orderBy('id', 'desc')->paginate(10);
        } else {

            $creator = Creator::where('phone', $request->query_data)->orderBy('id', 'desc')->paginate(10);
        }
        return response()->json([
            'status' => true,
            'data' => $creator,
            'message' => 'Creator Fetch Done'
        ]);
    }
    public function add_creator(Request $request)
    {
        $request->validate([
            'channel_name' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email|required|unique:creators,email',
            'phone' => 'required|numeric|unique:creators,phone_number',
            'password' => 'required'
        ]);
        $creator = Creator::create([
            'channel_name' => $request->channel_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone,
            'password' => bcrypt($request->password),
        ]);
        if ($creator) {
            return response()->json([
                'status' => true,
                'message' => 'New Creator Added SuccessFully'
            ]);
        }
    }
    public function delete_creator(Request $request)
    {
        $request->validate([
            'id' => 'required|exits:creators,id'
        ]);
        Creator::where('id', $request->id)->delete();
        UploadedVideos::where('creator_id', $request->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'deleted executed',
        ]);
    }
    public function ban_creator(Request $request)
    {
        $request->validate([
            'id' => 'required|exits:creators,id'
        ]);
      //  UploadedVideos::where('creator_id', $request->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Banned SuccessFully',
        ]);
    }
}
