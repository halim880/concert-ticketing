<?php

namespace Tests\Feature;

use App\Models\Consert;
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
}
