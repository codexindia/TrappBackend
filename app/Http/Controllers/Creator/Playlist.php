<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Playlist as PlaylistDB;
use Illuminate\Support\Facades\Storage;
class Playlist extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $Getlist = PlaylistDB::where('creator_id',$request->user()->id)->orderBy('id','desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $Getlist
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'thumbnail' => 'required|image|max:1024',
        ]);
        $update_values['playlist_name'] = $request->name;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = Storage::put('public/videos/thumbnail', $request->file('thumbnail'));
            $update_values['thumbnail'] = $thumbnail;
        }
        $update_values['creator_id'] = $request->user()->id;
        PlaylistDB::create($update_values);
        return response()->json([
            'status' => true,
            'message' => 'success',
          
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:playlists,id'
        ]);
        PlaylistDB::find($request->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'success',
          
        ]);
    }
}
