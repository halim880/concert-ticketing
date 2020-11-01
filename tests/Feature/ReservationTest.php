<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\NotEnoughTicketsExeption;
use App\Models\Consert;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function calculating_the_total_cost()
    {
        $consert = Consert::factory()->published()->create(['ticket_price'=> 1200]);
        $consert->addTickets(5);

        $tickets = $consert->findTickets(3);

        $reservation = new Reservation($tickets, 'akash@gmail.com');

        $this->assertEquals(3600, $reservation->totalCost());
    }

    /** @test */
    public function retrieving_the_reserved_tickets()
    {
        $this->withoutExceptionHandling();
        $consert = Consert::factory()->published()->create(['ticket_price'=> 1200]);
        $consert->addTickets(5);

        $tickets = $consert->reserveTicket(3);

        $reservation = new Reservation($tickets, 'akash@gmail.com');


        $this->assertEquals($tickets, $reservation->getTickets());
    }

        /** @test */
    public function cannot_reserved_tickets_that_already_resersed(){

        $this->withoutExceptionHandling();
        $consert = Consert::factory()->published()->create();
        $consert->addTickets(3);
        $this->assertEquals(3, $consert->remainingTickets());


        $consert->orderTickets('akash@gmail.com', 2);

        try {
            $consert->reserveTicket(2);
        } 
        catch (NotEnoughTicketsExeption $th) {

            $this->assertEquals(1, $consert->remainingTickets());
            return ;
        }   

        $this->fail('There is no anough tickets remaining');
    
    }

            /** @test */
    public function can_reserve_available_tickets(){

        $this->withoutExceptionHandling();
        $consert = Consert::factory()->published()->create();
        $consert->addTickets(20);
        
        $this->assertEquals(20, $consert->remainingTickets());

        $consert->reserveTicket(15);

        $this->assertEquals(5, $consert->remainingTickets());

    }

    /** @test */
    public function reserved_tickets_are_released_when_tickets_are_canceled(){

        $this->withoutExceptionHandling();
        $consert = Consert::factory()->published()->create();
        $consert->addTickets(5);
        
        $this->assertEquals(5, $consert->remainingTickets());

        $tickets = $consert->reserveTicket(3);

        $reservation = new Reservation($tickets, 'akash@gmail.com');

        $this->assertEquals(2, $consert->remainingTickets());

        $reservation->cancel();

        $this->assertEquals(5, $consert->remainingTickets());

    }

    
    /** @test */
    public function completing_order_reservation(){

        $this->withoutExceptionHandling();
        $consert = Consert::factory()->published()->create(['ticket_price'=> 1200]);
        $consert->addTickets(5);
        
        $this->assertEquals(5, $consert->remainingTickets());

        $tickets = $consert->reserveTicket(3);

        $paymentGateway = new FakePaymentGateway;


        $reservation = new Reservation($tickets, 'akash@gmail.com');

        $order = $reservation->complete($paymentGateway, $paymentGateway->getValidTestToken());

        $this->assertEquals('akash@gmail.com', $order->email);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(3600, $order->amount);
    }
}
