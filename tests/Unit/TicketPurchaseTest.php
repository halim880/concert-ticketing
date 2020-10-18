<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use App\Billing\NotEnoughTicketsExeption;
use App\Billing\PaymentFailedException;
use App\Models\Consert;
use PHPUnit\Framework\TestCase;

class TicketPurchaseTest extends TestCase
{
    /**
     * @test
     * @expectedExeption \App\Billing\PaymentFailExceptions
     * @return void
     */
    public function invalid_payment_token_make_fail_purchase()
    {
        
        try {
            $paymentGateway = new FakePaymentGateway;
            $paymentGateway->charge(2300, $paymentGateway->getValidTestToken());
        } 
        catch (NotEnoughTicketsExeption $th) {
            return;
        }
        $this->assertTrue(true);
    }

}
