<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StickersList;
class StickersManagement extends Controller
{
    public function fetch(Request $request)
    {
       $data = StickersList::orderBy('price','asc')->get(['name','id','price','sticker_src']);
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'fetched'
        ]);
    }
}
