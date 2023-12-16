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
            'first_name' => 'required',
            'last_name' => 'required',
            'channel_logo' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'channel_banner' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'channel_name' => 'required',
        ]);
        $update_values = array();
        $update_values['channel_name'] = $request->channel_name;
        $update_values['email'] = $request->email;
        $update_values['first_name'] = $request->first_name;
        $update_values['last_name'] = $request->last_name;
        if ($request->has('password')) {
            $update_values['password'] = bcrypt($request->password);
        }
        if ($request->hasFile('channel_logo')) {
            $image_path = Storage::put('public/creators/profiles', $request->file('channel_logo'));
            $update_values['channel_logo'] = $image_path;
        }
        if ($request->hasFile('channel_banner')) {
            $image_path = Storage::put('public/creators/profiles', $request->file('channel_banner'));
            $update_values['channel_banner'] = $image_path;
        }
        $creator = Creator::find($request->user()->id);
       
        $creator->update($update_values);
        return response()->json([
            'status' => true,
            'message' => 'User Updated SuccessFully',
        ]);
    }
}
