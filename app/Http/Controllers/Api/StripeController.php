<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoinBundle;
use Illuminate\Support\Facades\Log;
class StripeController extends Controller
{
    private $stripe;
    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    }
    public function CallSubsCription()
    {
      
     
         return $this->stripe->checkout->sessions->create([
            'success_url' => 'https://example.com/success',
            'line_items' => [
              [
                'price' => 'price_1Odd84EWrX6kyCsNluSkvWKA',
                'quantity' => 1,
              ],
            ],
            'mode' => 'subscription',
        ]);
        
    }
    public function CheckSubscription(Request $request){

        $request->validate([
            'sub_id' => 'required',
        ]);
        return $this->stripe->subscriptions->retrieve($request->sub_id, [])->status;
    }
    public function BuyCoins(Request $request)
    {



        $request->validate([
         'coin_bundle_id' => 'required|exists:coin_bundles,id',
        ]);
        $payment_id = 'TRP'.time();
        $coinData = CoinBundle::find($request->coin_bundle_id);
        $price_id = $this->stripe->prices->create([
            'currency' => 'usd',
            'unit_amount' => $coinData->price*100,
            'product_data' => ['name' => $coinData->coins.' Trapp Coins'],
          ])->id;
          $payment_init = $this->stripe->checkout->sessions->create([
            
            'client_reference_id' =>  $payment_id,
            'success_url' => 'https://example.com/success',
            'line_items' => [
              [
                'price' => $price_id,
                'quantity' => 1,
              ],
            ],
            'mode' => 'payment',
        ]);
       
        return response()->json([
         'status' => true,
         'payment_link' => $payment_init->url,
         'message' => 'Payment INIT SuccessFully'
        ]);
    }
    public function webhook(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $endpoint_secret = 'whsec_b52a89681a8728a98b5a8440755b81c7c8ccc2e59ea7a6e72e44c985ac022a4b';
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        
        try {
          $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
          );
        } catch(\UnexpectedValueException $e) {
          // Invalid payload
          return response('Invalid payload',400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
          // Invalid signature
          return response('Invalid signature',400);
        
        }
        
        // Handle the event
        switch ($event->type) {
            case 'checkout.session.async_payment_failed':
                $session = $event->data->object;
                Log::info($session);
            case 'checkout.session.async_payment_succeeded':
                $session = $event->data->object;
                Log::info($session);
          default:
            return 'Received unknown event type ' . $event->type;
        }
        
    }
}
