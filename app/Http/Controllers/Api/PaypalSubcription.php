<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalSubcription extends Controller
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new PayPalClient;
        //$this->provider->setApiCredentials(config('paypal'));
        $this->provider->getAccessToken();
    }
    public function test($amount = 7, $paypalPlanId = "P-89T130169W294933AMWHQJXA")
    {
        return $this->provider->addProductById('PROD-35J84284BS393300E')
            ->addBillingPlanById('P-12H508389L3242414MWH6GGI')
            ->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
            ->setupSubscription('Sudipto Bain', 'sb-no9jq28956608@personal.example.com', Carbon::now()->addDays(1));
    }
}
