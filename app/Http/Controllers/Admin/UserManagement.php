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
            $users = User::with(['UserBlocked:id,reason','UserSubcription:id,status'])
            ->orderBy('id', 'desc')->paginate(10);
           
        } else {

            $users = User::where('phone', $request->query_data)
            ->orderBy('id', 'desc')
            ->paginate(10);
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
        User::find($request->user_id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'User Deleted SuccessFully',
        ]);
    }
    public function ban_user(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id|unique:user_bands,user_id',
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
    public function unban_user(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:user_bands,user_id',
          
        ]);
        UserBand::where('user_id' ,$request->user_id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'User Unbanned SuccessFully',
        ]);
    }
}
