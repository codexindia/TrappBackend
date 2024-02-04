<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscriptions;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CoinBundle;
use Illuminate\Support\Facades\Log;
use App\Models\UserOrders;

class StripeController extends Controller
{
    public $redirect_url = 'https://trapp-sigma.vercel.app/orderStatus';
    private $stripe;
    public function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
    }
    public function CallSubsCription(Request $request)
    {

        $order_id = "TRP" . time();

        $payment_init = $this->stripe->checkout->sessions->create([
            'success_url' => $this->redirect_url . '/' . $order_id,
            'line_items' => [
                [
                    'price' => 'price_1Odd84EWrX6kyCsNluSkvWKA',
                    'quantity' => 1,
                ],
            ],
            'mode' => 'subscription',
        ]);
        $price = $this->stripe->prices->retrieve('price_1Odd84EWrX6kyCsNluSkvWKA', []);
        $createOrder = new UserOrders();
        $createOrder->user_id = $request->user()->id;
        $createOrder->session_id = $payment_init->id;
        $createOrder->order_id = $order_id;
        $createOrder->product_id = $request->coin_bundle_id;
        $createOrder->product_type = 'subscription';
        $createOrder->price = $price->unit_amount / 100;
        $createOrder->description = env('APP_NAME') . ' Subscription 1 Month';
        $createOrder->type = 'subscription';
        $createOrder->status = 'open';
        $createOrder->save();
        return response()->json([
            'status' => true,
            'payment_link' => $payment_init->url,
            'message' => 'Payment INIT SuccessFully'
        ]);
    }
    public function CheckSubscription(Request $request)
    {
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
        $order_id = "TRP" . time();


        $coinData = CoinBundle::find($request->coin_bundle_id);
        $price_id = $this->stripe->prices->create([
            'currency' => 'usd',
            'unit_amount' => $coinData->price * 100,
            'product_data' => ['name' => $coinData->coins . ' Trapp Coins'],
        ])->id;
        $payment_init = $this->stripe->checkout->sessions->create([


            'success_url' => $this->redirect_url . '/' . $order_id,
            'line_items' => [
                [
                    'price' => $price_id,
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
        ]);
        if ($payment_init->status != "open") {
            return response()->json([
                'status' => false,

                'message' => 'Payment INIT UnSuccessFully'
            ]);
        }
        $createOrder = new UserOrders();
        $createOrder->user_id = $request->user()->id;
        $createOrder->session_id = $payment_init->id;
        $createOrder->order_id = $order_id;
        $createOrder->product_id = $request->coin_bundle_id;
        $createOrder->product_type = 'coins';
        $createOrder->price = $coinData->price;
        $createOrder->description = $coinData->coins . ' ' . env('APP_NAME') . ' Coins';
        $createOrder->type = 'payment';
        $createOrder->status = 'open';
        $createOrder->save();

        return response()->json([
            'status' => true,
            'payment_link' => $payment_init->url,
            'message' => 'Payment INIT SuccessFully'
        ]);
    }
    public function webhook(Request $request)
    {
        $payload = @file_get_contents('php://input');
        $endpoint_secret = ENV('STRIPE_WEBHOOK');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }
        $session = $event->data->object;
        // Handle the event
        switch ($event->type) {

            case 'checkout.session.completed':

                $pending_order = UserOrders::where([
                    'session_id' => $session->id,
                    'status' => 'open'
                ])->first();
                if ($pending_order != null) {
                    $up_data = array(
                        'status' => $session->status,
                        'payment_id' => $session->payment_intent
                    );
                    if (isset($session->subscription)) {
                        $up_data['subscription_id'] = $session->subscription;
                    }
                    $pending_order->update($up_data);

                    if ($pending_order->product_type == "coins") {
                        $coin = CoinBundle::find($pending_order->product_id);
                        credit_coin($pending_order->user_id, $coin->coins, "Coin Topup Through Online Gateway");
                    } elseif ($pending_order->product_type == "subscription") {
                        subscription_apply($pending_order->user_id, $session->subscription);
                    }
                }
                break;
            case 'checkout.session.expired':
                $pending_order = UserOrders::where([
                    'session_id' => $session->id,
                ])->delete();
                Log::info($session);
                break;
            case 'customer.subscription.deleted':
                remove_subscription($session->id);
                break;
            default:
                return 'Received unknown event type ' . $event->type;
        }
    }
    public function FetchOrder(Request $request)
    {
        $pending_order = UserOrders::where([
            'order_id' => $request->id,
        ])->select('id', 'product_type', 'order_id', 'price', 'description', 'status')->first();
        if ($pending_order != null)
            return response()->json([
                'status' => true,
                'data' => $pending_order,
                'message' => 'Order Fetched SuccessFully'
            ]);
        else
            return response()->json([
                'status' => false,
                'message' => 'Invalid Order ID Or Order Trashed'
            ]);
    }
    public function CancelSubscription(Request $request)
    {
        $check_sub = Subscriptions::where([
            'user_id' => $request->user()->id,
            'status' => 'active'
            ])->latest()->first();
        if ($check_sub != null) {
          
            if ($this->stripe->subscriptions->cancel($check_sub->subscription_id, []))
                return response()->json([
                    'status' => true,
                    'message' => 'Subscription Has Been Canceled'
                ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Something West Wrong'
        ]);
    }
}
