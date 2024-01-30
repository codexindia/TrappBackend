<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoinBundle;
class WalletManage extends Controller
{
    public function create_new(Request $request)
    {
        $request->validate([
          
            'total_coins' => 'required|numeric',
            'price' => 'required|numeric',
        ]);

      

        $new = new CoinBundle;
        
        $new->coins = $request->total_coins;
        $new->price = $request->price;
        $new->save();
        return response()->json([
            'status' => true,
            'message' => 'Created SuccessFully'
        ]);
    }
    public function listCoinBundle()
    {
        $list = CoinBundle::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'data' => $list,
            'message' => 'Fetched SuccessFully'
        ]);
    }
    public function deleteCoinBundle(Request $request)
    {
        $request->validate([
            'coin_bundle_id' => 'required',
        ]);
        CoinBundle::findOrFail($request->coin_bundle_id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Deleted SuccessFully'
        ]);
    }
}
