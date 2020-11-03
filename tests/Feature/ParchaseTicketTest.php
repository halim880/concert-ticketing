<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\NotEnoughTicketsExeption;
use App\Billing\PaymentGateway;
use App\Facades\OrderConfirmationNumber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Consert;
use App\Models\Order;
use App\Models\OrderConfirmationNumberGenerator;
use App\Models\Ticket;
use Carbon\Carbon;
use Mockery;

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

        OrderConfirmationNumber::shouldReceive('generate')->andReturn('ORDERCONFIRMATION1234');


        $response = $this->json('POST', "consert/{$consert->id}/orders", [
            'email'=> 'akash@gmail.com',
            'ticket_quantity'=> 3,
            'payment_token'=> $paymentGateway->getValidTestToken(),
        ]);



        // dd($response);
        
        // $response->assertJson([
        //     'email'=> 'akash@gmail.com',
        //     'ticket_quantity'=> 3, 
        //     'total_charge'=> 9117,
        //     'confirmation_number'=> 'ORDERCONFIRMATION1234',
        // ]);

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
        $consert->addTickets(3);

        $response = $this->json('POST', "consert/{$consert->id}/orders", [
            'email'=> 'akash@gmail.com',
            'ticket_quantity'=> 3,
            'payment_token'=> 'invalid-payment-token',
        ]);
        
        $response->assertStatus(422);
        $this->assertFalse($consert->hasOrderFor('akash@gmail.com'));
        $this->assertEquals(3, $consert->remainingTickets());
    }

    /** @test */
    public function cannot_purchase_more_ticket_than_remaining(){

        $this->withoutExceptionHandling();
        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $consert = Consert::factory()->published()->create(['ticket_price'=> 1200]);
        $consert->addTickets(5);

        $this->orderTickets($consert, [
            'email'=> 'akash@gmail.com',
            'ticket_quantity'=> 3,
            'payment_token'=> $paymentGateway->getValidTestToken(),
        ]);

        $order = $consert->orders()->where('email', 'akash@gmail.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3600, $paymentGateway->totalCharge());
        $this->assertEquals(2, $consert->remainingTickets());

        $response = $this->orderTickets($consert, [
            'email'=> 'halim@gmail.com',
            'ticket_quantity'=> 3,
            'payment_token'=> $paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(422);
        $this->assertFalse($consert->hasOrderFor('halim@gmail.com'));
        $this->assertEquals(2, $consert->remainingTickets());
    }

    /** @test */
    public function trying_to_purchase_more_tickets_than_remaining_throw_exception(){

        $consert = Consert::factory()->published()->create();
        $consert->addTickets(3);

        try {
            $consert->reserveTicket(4, 'halim@gmail.com');
        } 
        catch (NotEnoughTicketsExeption $th) {
            $order = $consert->orders()->where('email', 'halim@gmail.com')->first();
            $this->assertNull($order);
            $this->assertEquals(3, $consert->remainingTickets());
            return ;
        }

        $this->fail('Unwanted Activity');
    }

    /** @test */
    public function tickets_cannot_order_those_are_already_purchased(){

        $consert = Consert::factory()->published()->create(['ticket_price'=>1000]);

        $consert->tickets()->saveMany(Ticket::factory(30)->create(['order_id'=>1]));
        $consert->tickets()->saveMany(Ticket::factory(20)->create());

        try {
            $consert->reserveTicket(21, 'halim@gmail.com');
        } 
        catch (NotEnoughTicketsExeption $th) {
            $this->assertEquals(20, $consert->remainingTickets());
            return ;
        }

        $this->fail('Unwanted');
    }


    /** @test */
    public function can_not_purchase_tickets_another_person_is_trying_to_purchase(){
        $this->withoutExceptionHandling();
        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $consert = Consert::factory()->published()->create(['ticket_price'=> 1200]);
        $consert->addTickets(3);


        $paymentGateway->beforeFirstCharge(function($paymentGateway) use ($consert){

            $requestA = $this->app['request'];
            $response = $this->orderTickets($consert, [
                'email'=> 'personB@gmail.com',
                'ticket_quantity'=> 1,
                'payment_token'=> $paymentGateway->getValidTestToken(),
            ]);

            $this->app['request'] = $requestA;

            $response->assertStatus(422);
            $this->assertFalse($consert->hasOrderFor('personB@gmail.com'));
            $this->assertEquals(0, $paymentGateway->totalCharge());
        });

        

        $this->orderTickets($consert, [
            'email'=> 'personA@gmail.com',
            'ticket_quantity'=> 3,
            'payment_token'=> $paymentGateway->getValidTestToken(),
        ]);
        
        // dd($consert->orders()->first()->toArray());

        $this->assertEquals(3600, $paymentGateway->totalCharge());
        
        $order = $consert->orders()->where('email', 'personA@gmail.com')->first();
        $this->assertTrue($consert->hasOrderFor('personA@gmail.com'));
        $this->assertNotNull($order);

        $this->assertEquals(3, $order->tickets->count());
    }

    public function orderTickets($consert, $params){
        return $this->json('POST', "/consert/{$consert->id}/orders", $params);
    }


}
