<?php

namespace App\Http\Controllers\Api;


use App\Events\MessageSent;
use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class ChatsController extends Controller
{
    public function sendMessage(Request $request)
    {

        $message = [

            "id" => $request->user()->id,
            "name" => $request->user()->name,
            "message" => $request->message,
            "avatar" => $request->user()->profile_pic
        ];

        return $message;
        event(new \App\Events\MessageSent($message));
        return "true";
    }
}
