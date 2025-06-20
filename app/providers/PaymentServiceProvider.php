<?php

namespace App\Providers;

use Stripe\StripeClient;
use PayPal\Rest\ApiContext;
use Yabacon\Paystack;
use App\Services\PaymentService;

class PaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PaymentService::class, function ($app) {
            $stripe = new StripeClient(getenv('STRIPE_SECRET_KEY'));
            $paypal = new ApiContext(getenv('PAYPAL_CLIENT_ID'), getenv('PAYPAL_CLIENT_SECRET'));
            $paystack = new Paystack(getenv('PAYSTACK_SECRET_KEY'));

            return new PaymentService($stripe, $paypal, $paystack);
        });
    }

    public function boot()
    {
        // No boot actions needed
    }
}
