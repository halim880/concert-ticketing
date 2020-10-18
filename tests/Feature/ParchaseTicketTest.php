<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\NotEnoughTicketsExeption;
use App\Billing\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Consert;
use App\Models\Order;
use Carbon\Carbon;

class ParchaseTicketTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function customers_can_purchase_published_consert_ticket(){
        $this->withoutExceptionHandling();
        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $consert = Consert::factory()->published()->create(['ticket_price'=> 3039]);
        $consert->addTickets(50);
        
        $response = $this->json('POST', "consert/{$consert->id}/orders", [
            'email'=> 'akash@gmail.com',
            'ticket_quantity'=> 3,
            'payment_token'=> $paymentGateway->getValidTestToken(),
        ]);
        
        $response->assertStatus(201);

        $this->assertEquals(9117, $paymentGateway->totalCharge());
        
        $order = $consert->orders()->where('email', 'akash@gmail.com')->first();
        $this->assertNotNull($order);

        $this->assertEquals(3, $order->tickets->count());
    }

     /** @test */
     public function customers_can_not_purchase_unpublished_consert_ticket(){
        // $this->withoutExceptionHandling();
        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $consert = Consert::factory()->unpublished()->create();

        $response = $this->json('POST', "consert/{$consert->id}/orders", [
            'email'=> 'akash@gmail.com',
            'ticket_quantity'=> 3,
            'payment_token'=> $paymentGateway->getValidTestToken(),
        ]);
        
        $response->assertStatus(404);
        $this->assertEquals(0, $consert->orders->count());
        $this->assertEquals(0, $paymentGateway->totalCharge());
    }
        /** @test */
    public function customers_can_order_consert_ticket(){
        $consert = Consert::factory()->create();
        $consert->addTickets(50);

        $order = $consert->orderTickets('akash@gmail.com', 3);

        $this->assertEquals(3, $order->tickets()->count());
        $this->assertEquals('akash@gmail.com', $order->email);
    }

        /** @test */
    public function email_is_required_to_purchase_ticket(){
        // $this->withoutExceptionHandling();

        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $consert = Consert::factory()->published()->create();

        $response = $this->json('POST', "consert/{$consert->id}/orders", [
            'ticket_quantity'=> 3,
            'payment_token'=> $paymentGateway->getValidTestToken(),
        ]);
        
        $response->assertStatus(422);
    }

     /** @test */
     public function ticket_quantity_is_required_to_purchase_ticket(){
        // $this->withoutExceptionHandling();

        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $consert = Consert::factory()->published()->create();

        $response = $this->json('POST', "consert/{$consert->id}/orders", [
            'email'=> 'akash@gmail.com',
            'payment_token'=> $paymentGateway->getValidTestToken(),
        ]);
        
        $response->assertStatus(422);
    }

     /** @test */
    public function token_is_required_to_purchase_ticket(){
        // $this->withoutExceptionHandling();

        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $consert = Consert::factory()->published()->create();

        $response = $this->json('POST', "consert/{$consert->id}/orders", [
            'email'=> 'akash@gmail.com',
            'payment_token'=> $paymentGateway->getValidTestToken(),
        ]);
        
        $response->assertStatus(422);
    }

    /** @test */
    public function payment_tokent_should_be_valid(){

        $this->withoutExceptionHandling();
        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $consert = Consert::factory()->published()->create();

        $response = $this->json('POST', "consert/{$consert->id}/orders", [
            'email'=> 'akash@gmail.com',
            'ticket_quantity'=> 3,
            'payment_token'=> 'invalid-payment-token',
        ]);
        
        $response->assertStatus(422);
    }

    /** @test */
    public function cannot_purchase_more_ticket_than_remaining(){
        $this->withoutExceptionHandling();
        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $consert = Consert::factory()->published()->create();
        $consert->addTickets(50);

        $this->json('POST', "consert/{$consert->id}/orders", [
            'email'=> 'akash@gmail.com',
            'ticket_quantity'=> 52,
            'payment_token'=> $paymentGateway->getValidTestToken(),
        ]);

        $order = $consert->orders()->where('email', 'akash@gmail.com')->first();
        $this->assertNull($order);
        $this->assertEquals(0, $paymentGateway->totalCharge());
        $this->assertEquals(50, $consert->remainingTickets());
    }

    /** @test */
    public function trying_to_purchase_more_tickets_than_remaining_throw_exception(){

        $consert = Consert::factory()->published()->create();
        $consert->addTickets(20);
        try {
            $consert->orderTickets('halim@gmail.com', 30);
        } 
        catch (NotEnoughTicketsExeption $th) {
            $order = $consert->orders()->where('email', 'halim@gmail.com')->first();
            $this->assertNull($order);
            $this->assertEquals(20, $consert->remainingTickets());
            return ;
        }

        $this->fail('Unwanted Activity');
    }

    /** @test */
    public function tickets_cannot_order_those_are_already_purchased(){

        $consert = Consert::factory()->published()->create();
        $consert->addTickets(20);
        $consert->orderTickets('akash@gmail.com', 15);

        try {
            $consert->orderTickets('halim@gmail.com', 6);
        } 
        catch (NotEnoughTicketsExeption $th) {
            $order = $consert->orders()->where('email', 'halim@gmail.com')->first();
            $this->assertNull($order);
            $this->assertEquals(5, $consert->remainingTickets());
            return ;
        }

        $this->fail('Unwanted');
    }

    /** @test */
    public function tickets_are_realesed_when_an_order_is_canceled(){

        $this->withoutExceptionHandling();
        $consert = Consert::factory()->published()->create();
        $consert->addTickets(20);
        $consert->orderTickets('akash@gmail.com', 15);

        $this->assertEquals(5, $consert->remainingTickets());
        $order = $consert->orders()->where('email', 'akash@gmail.com')->first();

        $order->cancel();
        $this->assertEquals(20, $consert->remainingTickets());
        $this->assertNull(Order::find($order->id));
    }
}
