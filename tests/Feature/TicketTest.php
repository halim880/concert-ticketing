<?php

namespace Tests\Feature;

use App\Facades\TicketCode;
use App\Models\Consert;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;
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
    public function a_ticket_can_be_claim_for_an_order(){
        $order = Order::factory()->create();
        $ticket = Ticket::factory()->create();

        TicketCode::shouldReceive('generateFor')->with($ticket)->andReturn('TICKETCODE1');

        $this->assertNull($ticket->code);

        $ticket->claimFor($order);

        $this->assertContains($ticket->id, $order->tickets->pluck('id'));
        $this->assertEquals('TICKETCODE1', $ticket->code);
    }
}
