<?php

namespace Tests\Feature;

use App\Billing\PaymentFailedException;
use App\Billing\StripePaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Stripe\StripeClient;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $this->lastCharge = $this->lastCharge();
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function charges_with_valid_payment_token_are_success()
    {
        $paymentGateway = new StripePaymentGateway(env('STRIPE_SECRET'));

        $paymentGateway->charge(2500, $this->token());
       
        $this->assertCount(1, $this->newCharges());

        $this->assertEquals(2500, $this->lastCharge()->amount);
    }


    /** @test */
    public function payment_tokent_should_be_valid(){

        $this->withoutExceptionHandling();
        try {
            $paymentGateway = new StripePaymentGateway(env('STRIPE_SECRET'));
            $paymentGateway->charge(2500, 'invalid-payment-token');
        } 
        catch (PaymentFailedException $th) {
            $this->assertCount(0, $this->newCharges());
            return;
        }

        $this->fail('Payment failed exception is not thrown');
    }

    private function newCharges(){
        return $this->stripe->charges->all([
            'limit' => 1,
            'ending_before'=> $this->lastCharge->id,
        ])['data'];
    }

    private function lastCharge(){
        return $this->stripe->charges->all([
            'limit' => 1,
        ])['data'][0];
    }

    private function token(){
        return $this->stripe->tokens->create([
            'card' => [
              'number' => '4242424242424242',
              'exp_month' => 11,
              'exp_year' => date('Y')+1,
              'cvc' => '123',
            ],
          ]);
    }
}
