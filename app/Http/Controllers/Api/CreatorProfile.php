<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VideoAnalytics;

class CreatorProfile extends Controller
{
    public function follow(Request $request)
    {
        $request->validate(['creator_id' => 'required|exists:creators,id']);
        $result = follow($request->creator_id, $request->user()->id);
        if ($result == 1) {
            return response()->json([
                'status' => true,
                'message' => 'follow success'
            ]);
        } elseif( $result == 2) {
            return response()->json([
                'status' => true,
                'message' => 'unfollow success'
            ]);
        }else
            return response()->json([
                'status' => false,
                'message' => $result
            ]);
        }
    }
   
    

