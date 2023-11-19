<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AuthManager extends Controller
{
    public function login_submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);
        $guard = Auth::guard('creator');
        // return Creator::create([
        //     'name' => 'Trapp Creators',
        //     'email' => $request->email,
        //     'password' => bcrypt($request->password),
        // ]);
        if ($guard->attempt(['email' => $request->email, 'password' => $request->password])) {
            $creator = Creator::where('email', $request->email)->first();
            $creator->tokens()->delete();

            $token = $creator->createToken('auth_token')->plainTextToken;
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
