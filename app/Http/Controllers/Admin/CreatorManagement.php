<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use Illuminate\Http\Request;

class CreatorManagement extends Controller
{
    public function get_creator(Request $request)
    {
       
        if($request->query_data == "all"){
            $creator = Creator::orderBy('id','desc')->paginate(10);
        }else{
           
            $creator = Creator::where('phone',$request->query_data)->orderBy('id','desc')->paginate(10);
        }
        return response()->json([
            'status' => true,
            'data' => $creator,
            'message' => 'Creator Fetch Done'
        ]);
    }
}
