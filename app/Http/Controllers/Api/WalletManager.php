<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoinBundle;
use App\Models\CoinTransaction;
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
    public function TransactionFetch(Request $request)
    {
       $data = CoinTransaction::where([
            'user_type' => 'user',
            'user_id' => $request->user()->id
        ])->orderBy('id','desc')->select(
            'reference_id','coins','transaction_type','description','created_at'
        )->paginate(5);
        return response()->json([
          'status' => true,
          'data' => $data
        ]);
    }
}
