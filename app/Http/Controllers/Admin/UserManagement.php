<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserManagement extends Controller
{
    public function get_users(Request $request)
    {

        if ($request->query_data == "all") {
            $users = User::orderBy('id', 'desc')->paginate(10);
        } else {

            $users = User::where('phone', $request->query_data)->orderBy('id', 'desc')->paginate(10);
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
            'id' => 'required|exists:users,id',
        ]);
        User::find($request->id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'User Deleted SuccessFully',
        ]);
    }
}
