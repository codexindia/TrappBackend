<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StickersList;
use Illuminate\Support\Facades\Storage;

class StickersManagement extends Controller
{
    public function create_new(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stickers' => 'required|image',
        ]);

        $image_path = Storage::put('public/admin/stickers', $request->file('stickers'));
        $sticker_loc = $image_path;

        $new = new StickersList;
        $new->name = $request->name;
        $new->sticker_src = $sticker_loc;
        $new->price = $request->price;
        $new->save();
        return response()->json([
            'status' => true,
            'message' => 'Created SuccessFully'
        ]);
    }
    public function listSticker()
    {
        $list = StickersList::orderBy('id', 'desc')->get(['name', 'id', 'price', 'sticker_src']);
        return response()->json([
            'status' => true,
            'data' => $list,
            'message' => 'Fetched SuccessFully'
        ]);
    }
    public function deleteSticker(Request $request)
    {
        $request->validate([
            'sticker_id' => 'required',
        ]);
        StickersList::findOrFail($request->sticker_id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Deleted SuccessFully'
        ]);
    }
}
