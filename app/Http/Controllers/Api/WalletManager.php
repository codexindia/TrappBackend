<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoinBundle;

class WalletManager extends Controller
{
    public function GetCoinBundle(Request $request)
    {
        $coins = CoinBundle::orderBy('price', 'asc')->get();
        return response()->json([
            'status' => true,
            'coins_bundle' => $coins,
            'message' => 'coins reterive done'
        ]);
    }
}
