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

class ChatsController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'video_id' => 'required'
        ]);

        $message = [
            "created_at" => Carbon::now(),
            "id" => $request->user()->id,
            "name" => $request->user()->name,
            "message" => $request->message,
            "avatar" => $request->user()->profile_pic,
            "video_id" => $request->video_id,
        ];
        Message::create([
            "user_id" => $request->user()->id,
            "name" => $request->user()->name,
            "message" => $request->message,
            "avatar" => $request->user()->profile_pic,
            "video_id" => $request->video_id,
        ]);

        event(new \App\Events\MessageSent($message));
        return response()->json([
            'status' => true,
        ]);
    }
    public function fetch(Request $request)
    {
        $request->validate([
            'video_id' => 'required',
        ]);
        $data = Message::where('video_id', $request->video_id)->limit(70)->orderBy('id', 'desc')->get(['id','name', 'avatar', 'message', 'created_at']);
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
