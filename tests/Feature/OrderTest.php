<?php

namespace Tests\Feature;

use App\Facades\TicketCode;
use App\Models\Charge;
use App\Models\Consert;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Vinkla\Hashids\Facades\Hashids;

use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
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
        $this->withoutExceptionHandling();

        $consert = Consert::factory()->published()->create(['ticket_price'=>1000]);
        $consert->addTickets(3);

        $order = Order::factory()->create();

        $order->tickets()->saveMany($consert->tickets->take(2));

        $this->assertEquals(1, $consert->remainingTickets());

        $this->assertEquals(2, $order->tickets->count());
        
        $this->assertEquals('akash@gmail.com', $order->email);
    }

    /** @test */
    public function converting_to_array()
    {
        $consert = Consert::factory()->published()->create(['ticket_price'=> 1200]);
        $consert->addTickets(3);

        $order = Order::factory()->create();

        $order->tickets()->saveMany([
            Ticket::factory()->create(['code'=> 'TICKETCODE1']),
            Ticket::factory()->create(['code'=> 'TICKETCODE2']),
            Ticket::factory()->create(['code'=> 'TICKETCODE3']),
        ]);

        $result = $order->toArray();

        // dd($result);
        $this->assertEquals([
            'email'=> 'akash@gmail.com',
            'amount'=> 3600,
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'tickets'=> [
                ['code'=> 'TICKETCODE1'],
                ['code'=> 'TICKETCODE2'],
                ['code'=> 'TICKETCODE3'],
            ],
        ], $result);
        
    }

    /** @test */
    public function creating_an_order_for_tickets()
    {

        $tickets = collect([
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
            Mockery::spy(Ticket::class),
        ]);

        $charge = new Charge([
            'amount'=> 3600,
            'card_last_four'=> 4242,
        ]);

        $order = Order::forTickets($tickets, 'akash@gmail.com', $charge);

        $this->assertEquals('akash@gmail.com', $order->email);
        $this->assertEquals(3600, $order->amount);
        $this->assertEquals(4242, $order->card_last_four);

        $tickets->each->shouldHaveReceived('claimFor', [$order]);
    }


    /** @test */
    public function a_ticket_can_be_reserved(){
        $ticket = Ticket::factory()->create();
        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();
        $this->assertNotNull($ticket->reserved_at);
    }

}
