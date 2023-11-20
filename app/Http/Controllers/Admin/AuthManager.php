<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class AuthManager extends Controller
{
    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);
        $guard = Auth::guard('admin');
        // return Admin::create([
        //     'name' => 'TrappAdmin',
        //     'email' => $request->email,
        //     'password' => bcrypt($request->password),
        // ]);
        if ($guard->attempt(['email' => $request->email, 'password' => $request->password])) {
            $admin = Admin::where('email', $request->email)->first();
           // $admin->tokens()->delete();

            $token = $admin->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => true,
                'token' => $token,
                'message' => 'Login Success'
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Oppes! You have entered invalid credentials'
        ]);
    }
    public function forget_pass(Request $request)
    {
        return "Forget Password";
    }
}
