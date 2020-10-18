<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Billing\FakePaymentGateway;
use App\Models\Consert;

class FakePaymentGatewayTest extends TestCase
{
    /** @test */
    public function charges_with_valid_token_are_successful(){
        $this->withoutExceptionHandling();

        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(2300, $paymentGateway->getValidTestToken());

        $this->assertEquals(2300, $paymentGateway->totalCharge());
    }


}
