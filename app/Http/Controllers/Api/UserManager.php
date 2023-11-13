<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
//use App\Notifications\UserAlert;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Facades\CauserResolver;

class UserManager extends Controller
{
   
    public function get_current_user(Request $request)
    {
       
        return response()->json([
            'status' => true,
            'data' => $request->user(),
            'message' => 'User Retrived',
        ]);
    }
    public function update_user(Request $request)
    {
      
        $request->validate([
            'name' => 'required',
            'email' => 'email',
            'profile_pic' => 'image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ], [
            'name.required' => 'Please Enter Your name',
            'email.email' => 'Please Enter Valid Email',
            'profile_pic.image' => 'Upload your Valid profile picture'
        ]);
        $updated_filed = array(
            'name' => $request->name,
        );
        if ($request->hasFile('profile_pic')) {
            $image_path = Storage::put('public/users/profiles', $request->file('profile_pic'));
            $updated_filed['profile_pic'] = $image_path;
        }
        if ($request->has('email')) {
            $updated_filed['email'] = $request->email;
        }
        $user = User::find($request->user()->id);
       
        $user->update($updated_filed);
        $param['title'] = 'lorem ipsum dolor sit amet, consectet';
        $param['subtitle'] = 'lorem ipsum dolor sit amet, consectet lorem ipsum dolor sit amet, consectet lorem ipsum dolor sit amet, consectet';
      //  Notification::send($user, new UserAlert($param));
        return response()->json([
            'status' => true,
            'message' => 'User Updated SuccessFully',
        ]);
    }
   
}
