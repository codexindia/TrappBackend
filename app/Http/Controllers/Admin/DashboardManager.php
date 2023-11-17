<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardManager extends Controller
{
    public function get_counts(Request $request)
    {
        $counts = array();
        $counts["users"] = 50;
        $counts["creators"] = 50;
        $counts["videos"] = 0;
        $counts["live"] = 50;
        //$counts = json_encode($counts);
        return response()->json([
            'status' => true,
            'dash_counts' => $counts,
            'message' => 'Dashboard Retirved',
        ]);
    }
}
