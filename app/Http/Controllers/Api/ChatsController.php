<?php

namespace App\Http\Controllers\Api;


use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;
use App\Models\StickersList;

class ChatsController extends Controller
{
    public function SendSticker(Request $request)
    {
        $request->validate([
            'sticker_id' => 'required|exists:stickers_lists,id',
            'video_id' => 'required|exists:uploaded_videos,id'
        ]);
        
        $sticker = StickersList::find($request->sticker_id);
        
        $message = [
            "user_id" => $request->user()->id,
            "name" => $request->user()->name,
            "message_type" => 'stickers',
            "type" => "user",
            "avatar" => $request->user()->profile_pic,
            "video_id" => $request->video_id,
            "sticker" =>  $sticker->sticker_src,
        ];
        $status = debit_coin($message['user_id'], $sticker->price, "For Sending Gift Or Stickers To Creator");
        if(!$status){
            return response()->json([
                'status' => false,
                'message' => "You Don't Have Enough Coins",
            ]); 
        }
        
        $result = Message::create($message);
        
        event(new \App\Events\MessageSent($result));
        return response()->json([
            'status' => true,
        ]);
    }
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'video_id' => 'required'
        ]);

        $message = [
            "user_id" => $request->user()->id,
            "name" => $request->user()->name,
            "message_type" => 'plain_text',
            "message" => $request->message,
            "type" => "user",
            "avatar" => $request->user()->profile_pic,
            "video_id" => $request->video_id,
        ];
        $result = Message::create($message);

        event(new \App\Events\MessageSent($result));
        return response()->json([
            'status' => true,
        ]);
    }
    public function fetch(Request $request)
    {
        $request->validate([
            'video_id' => 'required',
        ]);
        $data = Message::where('video_id', $request->video_id)->limit(200)->orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
