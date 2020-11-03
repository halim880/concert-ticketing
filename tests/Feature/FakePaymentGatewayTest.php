<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Billing\FakePaymentGateway;
use App\Models\Consert;

class FakePaymentGatewayTest extends TestCase
{

    protected function getPaymentGateway(){
        return new FakePaymentGateway;
    }

    /** @test */
    public function charges_with_valid_token_are_successful(){
        $this->withoutExceptionHandling();

        $paymentGateway = $this->getPaymentGateway();

        $charge = $paymentGateway->charge(2300, $paymentGateway->getValidTestToken('1234567890124242'));

        $this->assertEquals(2300, $charge->amount());
    }


    /** @test */

    public function running_a_hook_before_charging(){
        $paymentGateway = new FakePaymentGateway;

        $callbackTimes = 0;

        $paymentGateway->beforeFirstCharge(function ($paymentGateway) use (&$callbackTimes){
            $callbackTimes++;
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
            $this->assertEquals(2500, $paymentGateway->totalCharge());
        });

        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals($callbackTimes, 1);

        $this->assertEquals(5000, $paymentGateway->totalCharge());
    }

     /** @test */

     public function can_get_details_of_a_successful_charge(){
        $paymentGateway = $this->getPaymentGateway();
        $charge = $paymentGateway->charge(2500, $paymentGateway->getValidTestToken('1234567890124242'));


        $this->assertEquals('4242', $charge->getLastFour());
        $this->assertEquals(2500, $charge->amount());

    }
}
