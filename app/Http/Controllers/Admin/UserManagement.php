<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserBand;
use Illuminate\Http\Request;
use App\Models\User;

class UserManagement extends Controller
{
    public function get_users(Request $request)
    {

        if ($request->query_data == "all") {
            $users = User::with('UserBlocked')
            ->orderBy('id', 'desc')->paginate(2)
            ->onEachSide(2);
           
        } else {

            $users = User::where('phone', $request->query_data)
            ->orderBy('id', 'desc')
            ->paginate(2) ->onEachSide(2);
        }
        return response()->json([
            'status' => true,
            'data' => $users,
            'message' => 'User Fetch Done'
        ]);
    }
    public function delete_user(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        User::find($request->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'User Deleted SuccessFully',
        ]);
    }
    public function ban_user(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reasons' => 'required'
        ]);
        UserBand::create([
          'user_id' => $request->user_id,
          'reason' => $request->reasons,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'User banned SuccessFully',
        ]);
    }
}
