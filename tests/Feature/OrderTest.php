<?php

namespace Tests\Feature;

use App\Models\Consert;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function customers_can_order_consert_ticket(){
        $consert = Consert::factory()->create();
        $consert->addTickets(50);

        $order = $consert->orderTickets('akash@gmail.com', 3);

        $this->assertEquals(3, $order->tickets->count());
        $this->assertEquals('akash@gmail.com', $order->email);
    }

    /** @test */
    public function converting_to_array()
    {
        $consert = Consert::factory()->published()->create(['ticket_price'=> 1200]);
        $consert->addTickets(5);
        $order = $consert->orderTickets('akash@gmail.com', 5);

        $result = $order->toArray();

        $this->assertEquals([
            'email'=> 'akash@gmail.com',
            'ticket_quantity'=> 5,
            'amount'=> 6000,
        ], $result);
        
    }

    /** @test */
    public function creating_an_order_for_tickets()
    {
        $consert = Consert::factory()->published()->create(['ticket_price'=> 1200]);
        $consert->addTickets(5);

        $this->assertEquals(5, $consert->remainingTickets());

        $order = Order::forTickets($consert->findTickets(3), 'akash@gmail.com', 3600);

        $this->assertEquals('akash@gmail.com', $order->email);
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(3, $order->ticketQuantity());
        $this->assertEquals(2, $consert->remainingTickets());
    }


    /** @test */
    public function a_ticket_can_be_reserved(){
        $ticket = Ticket::factory()->create();
        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();
        $this->assertNotNull($ticket->reserved_at);
    }
}
