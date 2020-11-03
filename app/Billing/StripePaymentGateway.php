<?php

namespace App\Billing;

use App\Models\Charge;

class StripePaymentGateway implements PaymentGateway{

    private $tokens;
    private $stripe;

    public function __construct($api_key)
    {
        $this->tokens = collect();
        $this->stripe = new \Stripe\StripeClient($api_key);
    }

    public function charge($amount, $token)
    {
        try {
            $stripeCharge = $this->stripe->charges->create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' =>  $token,
                'description' => 'My First Test Charge (created for API docs)',
            ]);

            return new Charge([
                'amount'=> $stripeCharge['amount'],
                'card_last_four'=> $stripeCharge['source']['last4'],
            ]);

        } catch (\Stripe\Exception\InvalidRequestException $th) {
            throw new PaymentFailedException;
        }

        
    }

    public function getValidTestToken($cardNumber = '4242424242424242')
    {
        return $this->stripe->tokens->create([
            'card' => [
              'number' => '4242424242424242',
              'exp_month' => 11,
              'exp_year' => date('Y')+1,
              'cvc' => '123',
            ],
        ]);
    }

    public function newChargesDuring($callback){
        $latestCharge = $this->lastCharge();
        $callback($this);
        $newCharges = $this->newChargesDuring($latestCharge);

        dd($newCharges);
    }

    private function lastCharge(){
        return $this->stripe->charges->all([
            'limit' => 1,
        ])['data'][0];
    }

    private function newChargesSince($charge){
        return $this->stripe->charges->all([
            'limit' => 1,
            'ending_before'=> $charge->id,
        ])['data'];
    }
}
