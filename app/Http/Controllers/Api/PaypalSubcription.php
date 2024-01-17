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
        $this->provider->setApiCredentials(config('paypal'));
        $this->provider->getAccessToken();
    }
    public function test($amount = 7, $paypalPlanId = "P-89T130169W294933AMWHQJXA")
    {
        
        return $this->provider->addProduct('Demo Product', 'Demo Product', 'SERVICE', 'SOFTWARE')
        ->addCustomPlan('Demo Plan', 'Demo Plan', 150, 'MONTH', 3)
        ->setReturnAndCancelUrl('https://example.com/paypal-success', 'https://example.com/paypal-cancel')
        ->setupSubscription('John Doe', 'john@example.com', Carbon::now()->addDays(1));
      
      
    }
}
