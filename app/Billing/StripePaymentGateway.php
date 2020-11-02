<?php

namespace App\Billing;

class StripePaymentGateway implements PaymentGateway{

    private $api_key;
    private $stripe;

    public function __construct($api_key)
    {
        $this->stripe = new \Stripe\StripeClient($api_key);
    }

    public function charge($amount, $token)
    {
        try {
            $this->stripe->charges->create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' =>  $token,
                'description' => 'My First Test Charge (created for API docs)',
            ]);
        } catch (\Stripe\Exception\InvalidRequestException $th) {
            throw new PaymentFailedException;
        }
    }
}
