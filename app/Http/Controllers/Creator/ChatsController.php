<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ChatsController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'video_id' => 'required'
        ]);
        $user = $request->user();

        $message = [
           
            "user_id" => $user->id,
            "name" =>$user->channel_name,
            "type" => "creator",
            "message_type" => 'plain_text',
            "message" => $request->message,
            "avatar" => $user->channel_logo,
            "video_id" => $request->video_id,
        ];
        $result = Message::create($message);

        event(new \App\Events\MessageSent($result, 'creator'));
        return response()->json([
            'status' => true,
        ]);
    }
    public function fetch(Request $request)
    {
        $request->validate([
            'video_id' => 'required',
        ]);
        $data = Message::where('video_id', $request->video_id)->limit(200)->orderBy('id', 'desc')->get(['id', 'type','name', 'avatar', 'message', 'created_at']);
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
