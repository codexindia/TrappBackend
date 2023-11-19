<?php

namespace App\Http\Controllers\Creator;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Creator;
use Illuminate\Http\Request;

class ProfileManager extends Controller
{
    public function get_profile(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => true,
            'data' => $user,
            'message' => 'profile reteive successfully'
        ]);
    }
    public function edit_profile(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required',
            'profile_pic' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048'
        ]);
        $update_values = array();
        $update_values['email'] = $request->email;
        $update_values['name'] = $request->name;
        if ($request->has('password')) {
            $update_values['password'] = bcrypt($request->password);
        }
        if ($request->hasFile('profile_pic')) {
            $image_path = Storage::put('public/creators/profiles', $request->file('profile_pic'));
            $update_values['profile_pic'] = $image_path;
        }
        $creator = Creator::find($request->user()->id);
       
        $creator->update($update_values);
        return response()->json([
            'status' => true,
            'message' => 'User Updated SuccessFully',
        ]);
    }
}
