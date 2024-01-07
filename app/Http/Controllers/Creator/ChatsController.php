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
            "created_at" => Carbon::now(),
            "id" => $user->id,
            "name" =>$user->channel_name,
            "type" => "creator",
            "message" => $request->message,
            "avatar" => $user->channel_logo,
            "video_id" => $request->video_id,
        ];
        Message::create([
            "user_id" => $user->id,
            "name" => $user->channel_name,
            "message" => $request->message,
            "type" => "creator",
            "avatar" => $user->channel_logo,
            "video_id" => $request->video_id,
        ]);

        event(new \App\Events\MessageSent($message, 'creator'));
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
