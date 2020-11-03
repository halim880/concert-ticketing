<?php
namespace App\Billing;

use App\Models\Charge;


class FakePaymentGateway implements PaymentGateway{


    private $tokens;
    private $charges;
    private  $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
        $this->tokens = collect();
    }

    public function getValidTestToken($cardNumber = '4242424242424242'){

        $pool = "ABCDEFGHIKLMNOPQRSTUVWXYZ23456789";
        $token = substr(str_shuffle(str_repeat($pool, 24)), 0, 16);

        $this->tokens[$token] = $cardNumber;

        return $token;
    }

    public function charge($amount, $token){


        if($this->beforeFirstChargeCallback !== null){
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }

        if(!$this->tokens->has($token)){
            throw new PaymentFailedException;
        }

        return $this->charges[] = new Charge([
            'amount' => $amount,
            'card_last_four' => substr($this->tokens[$token], -4),
        ]);
    }

    public function totalCharge(){
        return $this->charges->map->amount()->sum();
    }

    public function beforeFirstCharge($callback){
        $this->beforeFirstChargeCallback = $callback;
    }

}