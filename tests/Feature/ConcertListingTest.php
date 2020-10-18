<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Consert;
use Carbon\Carbon;

class ConcertListingTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function user_can_see_consert_details(){
        $this->withoutExceptionHandling();

        $consert = Consert::factory()->make();

        $consert->date = Carbon::parse('2020-12-14 17:00:00');

        $view = $this->view('consert.show', compact('consert'));
        $view->assertSee($consert->title);
        $view->assertSee($consert->formatted_date);
        $view->assertSee($consert->formatted_start_time);


    }

    /** @test */
    public function get_list_of_published_conserts(){
        $consertA = Consert::factory()->published()->create();
        $consertB = Consert::factory()->published()->create();
        $consertC = Consert::factory()->unpublished()->create();

        $conserts = Consert::published()->get();

        $this->assertTrue($conserts->contains($consertA));
        $this->assertTrue($conserts->contains($consertB));
        $this->assertFalse($conserts->contains($consertC));
    }

    // /** @test */
    public function unpublished_consert_cannot_be_seen(){
        $this->withoutExceptionHandling();

        $consert = Consert::factory()->create();

        $this->get('/consert/'.$consert->id)->assertResponseStatus(404);

    }

    /** @test */
    public function get_formatted_date(){
        $consert = Consert::factory()->make();
        $consert->date = Carbon::parse('2020-12-14 17:00:00');

        $this->assertEquals('December 14, 2020', $consert->formatted_date);
    }

    /** @test */
    public function get_formatted_start_time(){
        $this->withoutExceptionHandling();

        $consert = Consert::factory()->make([
            'date' => Carbon::parse('2020-12-14 17:00:00')
        ]);

        $this->assertEquals('5:00pm', $consert->formatted_start_time);
    }

    /** @test */
    public function get_formatted_ticket_price(){
        $this->withoutExceptionHandling();
        
        $consert = Consert::factory()->make();
        $consert->ticket_price = 3039;

        $this->assertEquals(30.39, $consert->formatted_ticket_price);
    }

    /** @test */
    public function tickects_can_be_added_to_a_consert(){
        $this->withoutExceptionHandling();

        $consert = Consert::factory()->published()->create();

        $consert->addTickets(50);
        $this->assertEquals(50, $consert->remainingTickets());
    }

    /** @test */
    public function remainimg_tickets_does_not_include_tickets_having_order_id(){
        $this->withoutExceptionHandling();

        $consert = Consert::factory()->published()->create();
        $consert->addTickets(50);
        $consert->orderTickets('akash@gmail.com', 30);

        $this->assertEquals(20, $consert->remainingTickets());
    }
}